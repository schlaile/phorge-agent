# Module Scope

## Core Concept

An `AgentThread` is a Phorge-native representation of agentic work attached to a
Phorge object.

The thread is not the agent runtime. It is the user-facing and auditable record
of what an external agent control plane is doing.

## Candidate Objects

- `AgentThread`
- `AgentThreadFrame`
- `AgentArtifact`
- `AgentQuestion`
- `AgentRequiredAction`
- `AgentBackendRef`

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

Initial artifact kinds:

- `test_run`
- `draft_change`
- `screenshot`
- `browser_check`
- `prototype_result`
- `implementation_result`
- `backend_resume_snapshot`

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

Use for the module itself:

- agent thread object model,
- UI,
- timeline frames,
- pending questions,
- artifacts,
- provider-neutral API,
- Alicia adapter examples.
