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
