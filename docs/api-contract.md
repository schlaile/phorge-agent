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

Authorization follows normal Conduit semantics: the method runs as the user
bound to the Conduit token. The token user must be able to edit the target
`object_phid`; visibility alone is not enough. In the Alicia integration, this
means the `phorge-agent` Phorge user should receive the same project or object
permissions that Alicia is allowed to act on.

External control plane creates or updates a thread for an object.

```json
{
  "external_thread_id": "session-123",
  "object_phid": "PHID-TASK-abc",
  "title": "Implement payment fallback",
  "status": "awaiting_chat",
  "agent_label": "alicia",
  "backend_kind": "codex_exec",
  "backend_thread_id": "thread-xyz"
}
```

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

```json
{
  "external_thread_id": "session-123",
  "author_phid": "PHID-USER-owner",
  "message_text": "Use variant B and continue.",
  "action_kind": "delegate_instruction"
}
```

## Design Notes

- Phorge should store enough state to render and audit the thread.
- Runtime-specific transcripts may stay in the external control plane.
- The external control plane remains responsible for execution policy.
- Phorge remains responsible for Phorge object visibility and local user
  permissions.
