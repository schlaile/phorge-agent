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

## Library Map

The initial skeleton ships with a small hand-maintained
`src/__phutil_library_map__.php`.

Once `arc` is available in the development environment, rebuild it with:

```sh
cd /path/to/phorge-agent
arc liberate src
```

## Storage

The first skeleton has no storage patches. Future versions that add
`PhalanxThread`, `PhalanxArtifact`, or related objects should provide a library-local
`PhabricatorSQLPatchList` and then run:

```sh
cd /path/to/phorge
./bin/storage upgrade
```
