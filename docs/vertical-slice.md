# Vertical Slice

This document defines the first useful Phalanx slice.

The goal is not to build a complete agent platform. The goal is to prove that a
Phorge-native agent thread adds value beyond ordinary Maniphest comments,
Conpherence chat, Harbormaster builds, Pholio mockups, and Differential review.

## Guiding Question

What is the smallest Phorge-native PhalanxThread that is obviously better than a
comment stream?

## V1 Scenario

A user asks Alicia to investigate or implement something from a Maniphest task.
Alicia starts an external session and reaches a decision point:

- the task has one active PhalanxThread,
- the thread is attached to the task,
- the thread status is `awaiting_chat`,
- the agent asks a product or implementation question,
- the primary contact is visible,
- the required action is visible,
- artifacts from prior work are visible,
- backend and resume context are visible enough to show continuity.

Example user-visible state:

- Status: `awaiting_chat`
- Agent: `alicia`
- Backend: `codex_exec`
- Question: "Please choose API variant A or B."
- Required action: `reply_in_chat`
- Artifacts: `Test run`, `Screenshot`, `Prototype result`
- Resume: external backend thread is known

## Minimum UI

The first UI should be a compact panel on a Maniphest task, not a full new
workspace.

The panel should show:

- current status,
- agent label,
- active backend kind,
- pending question,
- required action,
- primary contact,
- artifact list,
- last update,
- link to the full thread view if available.

The full thread view can initially be simple:

- header with status and linked object,
- timeline frames,
- artifact section,
- pending question section,
- raw external references hidden behind details.

## Minimum Thread Data

The first thread record needs:

- `external_thread_id`
- `object_phid`
- `title`
- `status`
- `agent_label`
- `backend_kind`
- `backend_thread_id`
- `external_resume_ref`
- `created_at`
- `updated_at`

## Minimum Frames

Only these frame kinds are needed for V1:

- `status`
- `agent_message`
- `tool_result`
- `artifact`
- `pending_question`
- `required_action`
- `failure`

Everything else can wait.

## Minimum Pending Question

V1 should treat pending questions as first-class state.

Fields:

- `question_kind`
- `prompt`
- `primary_contact_phid`
- `response_channel`
- `required_action_kind`
- `required_action`
- `default_action`

Out of scope for V1:

- structured answer option widgets,
- expiry timers,
- multi-party approval routing,
- automatic fallback execution.

## Minimum Artifact Model

V1 should not store large blobs in custom tables.

Fields:

- `external_artifact_id`
- `artifact_kind`
- `label`
- `summary`
- `file_phid`
- `object_phid`
- `external_ref`
- `payload_json`

Use existing Phorge objects where possible:

- Files for screenshots, logs, generated reports, and downloads.
- Pholio for visual mockups or reviewable screenshots.
- Differential for reviewable code changes.
- Harbormaster for build or test results.
- Pastes for standalone text artifacts.

## Minimum External API

V1 can be one provider-neutral upsert endpoint family:

- upsert thread,
- append frame,
- upsert artifact,
- mark pending question,
- clear pending question,
- receive user reply or control action.

The API can be refined later into Conduit methods if the module proves useful.

## Alicia Mapping

Alicia can produce the V1 data from its current plan snapshot:

- `execution_session_overviews[]` maps to PhalanxThread summaries.
- `pending_question` maps to PhalanxQuestion.
- `artifacts[]` plus full `execution_session_artifacts[]` map to PhalanxArtifact.
- backend fields map to PhalanxBackendRef.
- session events map to PhalanxFrame.

The first adapter does not need to mirror every Alicia event. It should select
frames that help a human understand and steer the work.

## Explicit Non-Goals for V1

- no direct agent execution in Phorge,
- no Codex-specific UI,
- no full transcript viewer,
- no complex permission delegation UI,
- no automatic Differential creation,
- no Harbormaster integration beyond links,
- no Pholio object creation unless already easy through existing APIs,
- no replacement for Conpherence.

## Success Criteria

The V1 is useful if a Phorge user can answer these questions without reading a
raw log or searching task comments:

- What is the agent doing?
- Is it waiting on me?
- What exactly does it need?
- What did it produce?
- Where can I inspect the relevant artifact?
- Can this work be resumed?
- Which external session does this refer to?

If those questions are answered clearly, the PhalanxThread concept has justified
itself.

## Next Implementation Step

Before writing Phorge module code, define a concrete example payload generated
from an Alicia `GET /plans/{id}/snapshot` response. That payload should become
the fixture for the first UI spike.
