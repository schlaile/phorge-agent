# phorge-agent

Agent threads and work products for Phorge.

`phorge-agent` is intended to become a provider-neutral Phorge module for
displaying, controlling, and auditing agentic work produced by external systems.
It is not an AI runtime. Systems such as Alicia, MCP-backed agents, or other
automation services should be able to attach to the same Phorge-native thread
model.

## Goals

- Represent long-running agent work as Phorge-native threads.
- Keep agent activity attached to existing Phorge objects such as Maniphest
  tasks, Differential revisions, Diffusion repositories, Pastes, and Mockups.
- Show structured timeline frames instead of raw log streams.
- Treat pending questions, required actions, approvals, and artifacts as
  first-class UI concepts.
- Keep runtime-specific details outside this module.

## Non-Goals

- Running agents directly.
- Embedding Codex-, Claude-, Alicia-, or MCP-specific runtime logic.
- Managing credentials or execution sandboxes.
- Replacing Phorge policy checks with external policy.

## Current Status

This repository is an early design and module-planning space. The first concrete
work is to define:

- an `AgentThread` object model,
- timeline frame types,
- an API contract for external agent control planes,
- the minimal Phorge core hooks needed for a clean module.

## Documents

- [Prior Art and Scope](docs/prior-art.md)
- [Module Scope](docs/module-scope.md)
- [API Contract Sketch](docs/api-contract.md)

## Relationship to Alicia

Alicia is expected to be the first backend integration, but this module should
not be Alicia-specific. Alicia owns planning, policy, harness/runtime selection,
session execution, and artifact production. `phorge-agent` should own the
Phorge-native representation of that work.
