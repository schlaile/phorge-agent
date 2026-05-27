# Module Scope

## Core Concept

`PhalanxThread` is a Phorge-native representation of agentic work attached to a
Phorge object.

The thread is not the agent runtime. It is the user-facing and auditable record
of what an external agent control plane is doing.

## Relationship to Existing Phorge Tools

Phalanx should not replace Maniphest, Differential, Conpherence,
Harbormaster, Feed, Herald, Files, Pastes, Pholio, or Conduit.

Those tools already model important parts of work:

- Maniphest says why work exists.
- Differential says what code is under review.
- Harbormaster says whether checks ran and passed.
- Conpherence supports human discussion.
- Feed and transactions record object history.
- Herald automates rule-based workflow.
- Files and Pastes hold supporting material.
- Pholio stores and reviews visual mockups.
- Conduit connects external systems.

`PhalanxThread` should connect these concepts around one missing object:
delegated, resumable agent work.

It should add value only where existing tools are too coarse:

- explicit session state,
- pending questions with routing,
- required actions,
- typed agent work products,
- backend and resume references,
- delegated-control policy,
- structured timeline frames instead of log or comment noise,
- object-spanning agent work across tasks, diffs, repositories, builds, and
  supporting artifacts.

If an interaction is just a human discussion, Conpherence is enough. If it is
just a task update, Maniphest comments are enough. If it is just a build result,
Harbormaster is enough. `PhalanxThread` is justified only when external agent work
needs to be observed, resumed, answered, approved, or stopped as an ongoing
unit.

## Candidate Objects

- `PhalanxThread`
- `PhalanxFrame`
- `PhalanxArtifact`
- `PhalanxQuestion`
- `PhalanxRequiredAction`
- `PhalanxBackendRef`

## Timeline Frames

Initial frame kinds:

- `status`
- `agent_message`
- `user_message`
- `tool_result`
- `artifact`
- `pending_question`
- `required_action`
- `approval`
- `failure`
- `resume_context`

## Pending Questions

Pending questions should be first-class UI state.

Suggested fields:

- `question_kind`
- `prompt`
- `primary_contact_phid`
- `response_channel`
- `required_action_kind`
- `required_action`
- `default_action`
- optional `expires_at`
- optional structured answer options

Question kinds:

- `implementation_clarification`
- `product_decision`
- `runtime_decision`
- `security_review`
- `external_communication`

## Required Actions

Suggested action kinds:

- `reply_in_chat`
- `open_or_reply_chat`
- `approve_review`
- `assign_harness`
- `prepare_workspace`
- `resume_session`
- `stop_session`

## Artifacts

Artifacts should be first-class work products, not log snippets.

Artifact content should use existing Phorge storage and object infrastructure
where possible. `PhalanxArtifact` should store meaning, lifecycle, and provenance;
Files, Pastes, Pholio, Differential, Harbormaster, and other objects should
store or review the underlying content.

In short:

> Files store artifact content; PhalanxArtifact stores artifact meaning.

Initial artifact kinds:

- `test_run`
- `draft_change`
- `screenshot`
- `browser_check`
- `visual_review`
- `mockup`
- `prototype_result`
- `implementation_result`
- `backend_resume_snapshot`

Suggested `PhalanxArtifact` links:

- `file_phid` for screenshots, logs, generated reports, and downloadable
  outputs.
- `object_phid` for related Phorge objects such as Pholio mockups,
  Differential revisions, Harbormaster builds, Pastes, or Files.
- `external_uri` or `external_ref` for content retained by an external control
  plane.
- `payload_json` for small structured summaries that do not warrant a separate
  object.

Examples:

- A screenshot can be a `PhalanxArtifact` with `artifact_kind=screenshot` and a
  `file_phid`.
- A visual mockup that needs review can point at a Pholio object with
  `object_phid`.
- A test run can store summary data in `payload_json` and link to a File or
  Harbormaster build for full output.
- A draft patch can link to a Differential revision, Paste, or File depending
  on how reviewable it is.
- A resume snapshot can remain a small structured payload or become a File if it
  is large.

## Repository Boundaries

### phorge

Use for upstreamable Phorge core changes only:

- generic hooks,
- generic policies,
- transaction or timeline extension points,
- broadly useful Conduit endpoints.

### phorge-patches

Use as a patch queue for small core changes.

### phorge-agent

Use for the Phalanx module itself:

- Phalanx thread object model,
- UI,
- timeline frames,
- pending questions,
- artifacts,
- provider-neutral API,
- Alicia adapter examples.
