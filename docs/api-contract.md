# API Contract Sketch

This document sketches the contract between Phorge Agent and an external agent
control plane such as Alicia.

The contract should remain provider-neutral.

## Thread Upsert

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
  "content_ref": "alicia://sessions/session-123/artifacts/artifact-456"
}
```

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
