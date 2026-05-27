# API Contract Sketch

This document sketches the contract between Phalanx and an external agent
control plane such as Alicia.

The contract should remain provider-neutral.

## Thread Upsert

Initial Conduit method:

```text
phalanx.thread.upsert
```

The method accepts the same top-level shape as the Alicia fixture:

- `thread`: required thread map,
- `pending_question`: optional active question map,
- `clear_pending_question`: optional boolean,
- `artifacts`: optional list of artifact maps.

The first implementation is intentionally an upsert, not an append-only event
stream. It writes current Phorge-facing state from an external control plane
snapshot.

Internally, Phalanx normalizes this shape into small provider-neutral contract
objects:

- `PhalanxThreadSnapshot`
- `PhalanxPendingQuestionSnapshot`
- `PhalanxArtifactSnapshot`

Conduit is the transport boundary; these snapshots are the module boundary that
backend adapters should target.

Authorization follows normal Conduit semantics, but Phalanx treats the token
user as a concrete agent actor, not as a global integration account. Alicia can
route different agents through different Conduit tokens, so Phorge policies can
distinguish implementers, reviewers, red-team agents, release agents, and other
roles.

For delegated work, the thread payload should include `delegated_user_phid`.
Phalanx then applies the local capability check as an intersection:

- the agent actor from the Conduit token must be able to edit `object_phid`,
- the delegated user must also be able to edit `object_phid`.

This prevents Alicia from using an agent token to expand a human user's rights.
If a later action needs stronger authority, such as landing, releasing,
deploying, or sending external mail, the external control plane should surface a
required action or approval instead of silently proceeding.

The first implementation enforces edit permission on `object_phid` for the
agent actor and, when `delegated_user_phid` is present, for the delegated user.
Higher-level capabilities such as `merge_approval`, `release_approval`, and
`security_approval` are represented in the provider-neutral contract first and
can grow into stricter Phorge-side checks over time.

External control plane creates or updates a thread for an object.

```json
{
  "external_thread_id": "session-123",
  "object_phid": "PHID-TASK-abc",
  "delegated_user_phid": "PHID-USER-owner",
  "title": "Implement payment fallback",
  "status": "awaiting_chat",
  "agent_label": "alicia",
  "route_kind": "primary_contact",
  "permission_level": "reply",
  "required_approval_kind": null,
  "chat_control_mode": "delegate_scoped",
  "review_policy": "mandatory_review_on_delegate",
  "backend_kind": "codex_exec",
  "backend_thread_id": "thread-xyz"
}
```

Suggested routing and approval fields mirror Alicia's existing session model:

- `delegated_user_phid`: user on whose behalf the agent is acting.
- `route_kind`: `primary_contact`, `allowed_responder`, `required_approver`,
  or `watcher`.
- `permission_level`: `inform`, `reply`, or `approve`.
- `required_approval_kind`: `merge_approval`, `release_approval`,
  `security_approval`, or `external_send_approval`.
- `chat_control_mode`: `visibility_only`, `control_limited`, or
  `delegate_scoped`.
- `review_policy`: for example `mandatory_review_on_delegate`.

Return shape:

```json
{
  "id": 12,
  "uri": "https://phorge.example.com/phalanx/thread/12/",
  "external_thread_id": "session-123",
  "object_phid": "PHID-TASK-abc",
  "status": "awaiting_chat"
}
```

## Thread Search

External control planes can read Phorge's current view of agent work with:

```text
phalanx.thread.search
```

Supported filters:

- `ids`
- `external_thread_ids`
- `object_phids`
- `statuses`
- `limit`
- `offset`

The method returns visible threads only. Visibility is determined by the target
`object_phid`, so Phalanx does not expose thread metadata for objects the token
user can not see.

Each result includes the thread fields, `properties`, the active
`pending_question` if one exists, current `artifacts`, and recorded `replies`.

Thread properties may include delivery configuration for outbound replies:

- `delivery_callback_uri`: HTTPS endpoint for reply/control-action delivery.
- `delivery_retry_mode`: `never` or `forever`.
- `delivery_hmac_key`: optional shared secret used to sign delivery payloads.

`delivery_hmac_key` is stored for delivery but is not returned by
`phalanx.thread.search`.

## Frame Append

External control plane appends structured timeline frames.

```json
{
  "external_thread_id": "session-123",
  "frame_kind": "pending_question",
  "summary": "Please choose API variant A or B.",
  "payload": {
    "question_kind": "product_decision",
    "primary_contact_phid": "PHID-USER-owner",
    "response_channel": "chat",
    "required_action_kind": "reply_in_chat",
    "default_action": "wait_for_response"
  }
}
```

## Artifact Upsert

External control plane publishes work products.

```json
{
  "external_thread_id": "session-123",
  "external_artifact_id": "artifact-456",
  "artifact_kind": "test_run",
  "label": "Test run",
  "summary": "go test ./... passed",
  "file_phid": "PHID-FILE-log",
  "object_phid": "PHID-HMBD-build",
  "external_ref": "alicia://sessions/session-123/artifacts/artifact-456",
  "payload": {
    "status": "passed",
    "command": "go test ./..."
  }
}
```

Artifact payloads should not duplicate Phorge storage. Large or reviewable
content should live in existing Phorge objects:

- Files for blobs, screenshots, logs, reports, and generated outputs.
- Pholio for visual mockups or reviewable screenshots.
- Differential for reviewable code changes.
- Harbormaster for build or test results.
- Pastes for text artifacts that should be readable as standalone documents.

The agent artifact record should explain what the linked content means in the
agent session.

## User Reply

Phorge sends user replies or control actions back to the external control plane.

Initial Conduit method:

```text
phalanx.thread.reply
```

The method records the reply in Phorge. If the thread has
`delivery_callback_uri`, Phalanx queues an outbound delivery. External control
planes can still poll `phalanx.thread.search` for recorded replies as a fallback.
The token user is stored as `author_phid` and must be able to edit the target
`object_phid`.

Replies use a delivery-state model inspired by chat read receipts:

- `recorded`: one check; the reply is safely stored in Phorge.
- `queued`: delivery to the external control plane is queued.
- `sent`: two checks; the callback returned a successful HTTP response.
- `acknowledged`: the external control plane confirmed processing.
- `failed`: delivery failed, but the reply remains stored and pollable.

```json
{
  "external_thread_id": "session-123",
  "message_text": "Use variant B and continue.",
  "action_kind": "delegate_instruction",
  "close_question": true,
  "properties": {
    "client": "phorge-ui"
  }
}
```

## Design Notes

- Phorge should store enough state to render and audit the thread.
- Runtime-specific transcripts may stay in the external control plane.
- The external control plane remains responsible for execution policy.
- Phorge remains responsible for object visibility, local user permissions, and
  approval records.
- Agent permissions and delegated-user permissions should combine by
  intersection. Approval and escalation should be explicit objects or required
  actions, not implicit rights expansion through an agent.
