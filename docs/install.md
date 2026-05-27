# Installation

Phalanx is intended to be loaded as an external Phorge library.

## Local Development

Clone this repository next to a Phorge checkout:

```sh
git clone git@github.com:schlaile/phorge-agent.git
```

Load the library through Phorge configuration:

```sh
cd /path/to/phorge
./bin/config set load-libraries '["/path/to/phorge-agent/src"]'
```

Then visit:

```text
/phalanx/
```

## Conduit Users

External control planes should call Phalanx with Conduit tokens owned by the
Phorge users that represent the concrete agent actors. A role-orchestrating
system such as Alicia can use different tokens for different agent roles, for
example implementer, reviewer, red-team, release, or operations agents.

For `phalanx.thread.upsert`, the token user must have edit permission on the
target `object_phid`. If the payload also includes `delegated_user_phid`, that
delegated user must have edit permission as well. This keeps agent work scoped
to the intersection of agent rights and user rights.

A generic `phorge-agent` user can still be useful for local development or
fallback automation, but it is not the preferred production authorization model
for multi-role agents.

## Library Map

The initial skeleton ships with a small hand-maintained
`src/__phutil_library_map__.php`.

Once `arc` is available in the development environment, rebuild it with:

```sh
cd /path/to/phorge-agent
arc liberate src
```

## Storage

The current module ships with `PhalanxThread`, `PhalanxArtifact`, and
`PhalanxQuestion` storage plus a library-local `PhabricatorSQLPatchList`. After
loading the library, run:

```sh
cd /path/to/phorge
./bin/storage upgrade
```
