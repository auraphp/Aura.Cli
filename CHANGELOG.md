# CHANGELOG

## Unreleased

### Changed

- Removed the `config/` directory and its `Aura\Cli\_Config\Common` Aura.Di
  container config, along with the `extra.aura` block in `composer.json`.
  Wire the objects yourself, or use `CliFactory`.
- Removed the hand-rolled `autoload.php`; use the Composer autoloader.
- Moved the documentation out of the README into `docs/`.
- Renamed `CHANGES.md` to `CHANGELOG.md`.

## 4.0.0-alpha1

- Update for PHP 7.2 or later

## 2.2.0

Add support for a space as separator between `--longopt` and its value; i.e.,
`--longopt val` (in addition to the existing `--longopt=val`).

## 2.1.2

Hygiene release: update license year, remove branch alias, and update docs.

## 2.1.1

This release modifies the testing structure and updates other support files.

## 2.1.0

This release adds a feature: _Getopt_ defintion strings now allow for noting
positional arguments using the `#argname` and `#argname?` notation, optionally
with an argument description. These are ignored by the _GetoptParser_ for
purposes of value-discovery, but the _Help_ class _does_ use them to
auto-generate usage lines.

## 2.0.4

This is a hygiene release to update documentation and support files.

## 2.0.3

- FIX: add explicit Help config for non-auto-resolved DI

## 2.0.2

- FIX: Context\Env::get() now additionally examines getenv() for values; fixes #47
- FIX: add explicit Context configs for non-auto-resolved DI

## 2.0.1

- TST: Update testing structure, disable auto-resolve in container tests
- DOC: Update README and docblocks
- FIX: #35, for when posix_isatty() function does not exist

## 2.0.0

First 2.0 stable release.

- DOC: Add docblocks, update README.
- ADD: Method GetoptFactory::getGetoptParser()
- FIX: #29, fixes color formatting
- REF: Various refactorings to split up Getopt tasks, add GetoptFactory, extract OptionParser, etc
- DEL: v1 config files
- ADD: v2 config class files
- CHG: When defining options, allow the '*' before *or* after the ':'
- BRK: Rename OptionParser to GetoptParser
- ADD: Help class
- FIX: Getopt alias ordering

## 2.0.0-beta1

Initial 2.0 beta release.
