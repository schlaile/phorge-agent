# Prior Art and Scope

Date: 2026-05-27

## Phorge Ponder: AI Integration

Phorge Ponder contains `Q185 Phorge and AI Integration`.

Current read:

- The topic is visible in the Phorge community.
- There does not appear to be a worked-out architecture yet.
- This is a useful future discussion anchor.

Source:

- https://we.phorge.it/ponder/

## Mozilla Review Helper for Phabricator

Mozilla added an AI review helper to a Phabricator fork. The visible scope is
focused on Differential review:

- request AI review from a revision,
- configure an external AI service URL,
- pass revision, diff, and user context to that service,
- return review-oriented output.

This proves that native AI affordances inside Phabricator-like UI are practical.
It is not the same as a generic agent-thread model:

- it is Differential-review-specific,
- it does not appear to model long-running sessions,
- it does not expose generic timeline frames,
- it does not model pending questions or required actions,
- it is not a provider-neutral agent work surface.

Sources:

- https://bugzilla.mozilla.org/show_bug.cgi?id=2010638
- https://github.com/mozilla-conduit/phabricator/commit/b57d964c0ad2bd007ecb4f48216634e6babe2c1b

## Phabricator and Phorge MCP Servers

Several MCP projects expose Phabricator or Phorge data through Conduit to LLMs
and agents.

These are complementary rather than competing:

- MCP gives agents access to Phorge.
- `phorge-agent` should make agent work visible, steerable, and auditable in
  Phorge.

Examples:

- https://playbooks.com/mcp/conduit
- https://mcp.so/server/phabricator-mcp-server
- https://playbooks.com/mcp/yushengauggie/phabricator-mcp-server

## External Automation and Bots

The Phorge ecosystem already has related conversations around external
automation actors, GitHub/GitLab bridges, and bot-driven workflows.

Relevant concerns:

- attribution,
- object linking,
- policy,
- review workflow,
- status transitions,
- avoiding opaque external automation.

Example:

- https://we.phorge.it/T15096

## Scope Boundary

`phorge-agent` should be a generic Phorge module. Alicia-specific details should
remain outside it:

- concrete harness execution,
- Codex or Claude CLI details,
- LXC/local-shell/codex-lb runtime logic,
- Alicia planning and persona resolution,
- internal retry and coordination policy,
- model selection and provider pricing.
