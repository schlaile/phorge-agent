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

## Conduit User

External control planes should call Phalanx with a Conduit token owned by a
dedicated Phorge user, such as `phorge-agent`. Phalanx evaluates writes as that
user.

For `phalanx.thread.upsert`, the token user must have edit permission on the
target `object_phid`. Grant this user access only to the projects, spaces, or
objects where agent work should be allowed.

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
