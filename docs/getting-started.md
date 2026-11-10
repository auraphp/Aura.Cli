# Getting Started

Aura.Cli provides the equivalent of request ( _Context_ ) and response
( _Stdio_ ) objects for the command line interface, including _Getopt_
support, and an independent _Help_ object for describing commands.

Install it via Composer:

```
composer require aura/cli
```

## Context Discovery

The _Context_ object provides information about the command line environment,
including any option flags passed via the command line. (This is the command
line equivalent of a web request object.)

Instantiate a _Context_ object using the _CliFactory_; pass it a copy of
`$GLOBALS`.

```php
<?php
use Aura\Cli\CliFactory;

$cli_factory = new CliFactory;
$context = $cli_factory->newContext($GLOBALS);
?>
```

You can access the `$_ENV`, `$_SERVER`, and `$argv` values with the `$env`,
`$server`, and `$argv` property objects, respectively. (Note that these
properties are copies of those superglobals as they were at the time of
_Context_ instantiation.) You can pass an alternative default value if the
related key is missing.

```php
<?php
// get copies of superglobals
$env    = $context->env->get();
$server = $context->server->get();
$argv   = $context->argv->get();

// the value of $_ENV['key'], or null if it is missing
$value = $context->env->get('key');

// the value of $_ENV['key'], or 'other_value' if it is missing
$value = $context->env->get('key', 'other_value');
?>
```

When a key is missing from the `$_ENV` copy, `$context->env->get()` also
checks the process environment with `getenv()` before returning the
alternative value. This finds environment variables even when `$_ENV` is not
populated, for example when `variables_order` in `php.ini` leaves out `E`.
