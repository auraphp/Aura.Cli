# Aura.Cli

Provides the equivalent of request ( _Context_ ) and response ( _Stdio_ )
objects for the command line interface, including _Getopt_ support, and an
independent _Help_ object for describing commands.

## Foreword

### Installation

This library requires PHP 7.2 or later; we recommend using the latest available version of PHP as a matter of principle.

It has no userland dependencies.

It is installable and autoloadable via Composer as [aura/cli](https://packagist.org/packages/aura/cli).

### Quality

[![Continuous Integration](https://github.com/auraphp/Aura.Cli/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/auraphp/Aura.Cli/actions/workflows/continuous-integration.yml)

To run the unit tests at the command line, issue `composer install` and then `composer test` at the package root. This requires [Composer](http://getcomposer.org/) to be available as `composer`.

This library attempts to comply with [PSR-1][], [PSR-2][], and [PSR-4][]. If
you notice compliance oversights, please send a patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md

### Community

To ask questions, provide feedback, or otherwise communicate with the Aura community, please join our [Google Group](http://groups.google.com/group/auraphp), follow [@auraphp on Twitter](http://twitter.com/auraphp), or chat with us on #auraphp on Freenode.

## Documentation

This package is fully documented [here](./docs/index.md).
