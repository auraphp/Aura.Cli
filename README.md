# Aura.Cli

## Overview

The Aura.Cli library provides the equivalent of request ( _Context_ ) and
response ( _Stdio_ ) objects for the command line interface, including
_Getopt_ support. Note that it does not provide commands or other
controller-like objects; it is strictly for environment discovery and standard
input/ouput operations.

### Installation and Autoloading

This library is installable via Composer and is registered on Packagist at
<https://packagist.org/packages/aura/cli>. Installing via Composer will set up
autoloading automatically.

Alternatively, download or clone this repository, then require or include its
_autoload.php_ file.

### Dependencies

As with all Aura libraries, this library has no external dependencies.

### Tests

This library has 100% code coverage. To run the library tests, first install
[PHPUnit][], then go to the library _tests_ directory and issue `phpunit` at
the command line.

[PHPUnit]: http://phpunit.de/manual/

### API Documentation

This library has embedded DocBlock API documentation. To generate the
documentation in HTML, first install [PHPDocumentor][] or [ApiGen][], then go
to the library directory and issue one of the following at the command line:

    # for PHPDocumentor
    phpdoc -d ./src/ -t /path/to/output/
    
    # for ApiGen
    apigen --source=./src/ --destination=/path/to/output/

You can then browse the HTML-formatted API documentation at _/path/to/output_.

[PHPDocumentor]: http://phpdoc.org/docs/latest/for-users/installation.html
[ApiGen]: http://apigen.org/#installation

### PSR Compliance

This library is compliant with [PSR-1][] and [PSR-2][]. If you notice
compliance oversights, please send a patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md


## The Context Object

The _Context_ object provides information about the command line environment,
including any option flags passed via the command line. (This is the command
line equivalent of a web request object.)

### Instantiation

Instantiate a _Context_ object like so:

```php
<?php
use Aura\Cli\Context;

$context = new Context(new Context\ValuesFactory($GLOBALS));
?>
```

### Usage

You can access the `$_ENV` and `$_SERVER` values with the `$env` and `$server`
property objects, respectively. (Note that these properties are copies of `$_ENV`
and `$_SERVER` as they were are at the time of _Context_ instantiation.) You
can pass an alternative default value if the related key is missing.

```php
<?php
// get a copy of all environment and server variables
$env    = $context->env->get();
$server = $context->server->get();

// equivalent to:
// $value = isset($_ENV['key']) ? $_ENV['key'] : null;
$value = $context->env->get('key');

// equivalent to:
// $value = isset($_ENV['key']) ? $_ENV['key'] : 'other_value';
$value = $context->env->get('key', 'other_value');
?>
```

## The Stdio Object

The _Stdio_ object to allows you to work with standard input/output streams.
(This is the command line equivalent of a web response object.)

### Instantiation

Instantiate a _Stdio_ object like so:

```php
<?php
use Aura\Cli\Stdio;

$stdio = new Stdio(
    new Stdio\Handle('php://stdin', 'r'),
    new Stdio\Handle('php://stdout', 'w+'),
    new Stdio\Handle('php://stderr', 'w+'),
    new Stdio\Vt100
);
?>
```

You can pick any stream you like for the _stdin_, _stdout_, and _stderr_
resource handles.

### Usage

The _Stdio_ object methods are ...

- `getStdin()`, `getStdout()`, and `getStderr()` to return the respective
  _Handle_ objects;

- `outln()` and `out()` to print to _stdout_, with or without a line ending;

- `errln()` and `err()` to print to _stderr_, with or without a line ending;

- `inln()` and `in()` to read from _stdin_ until the user hits enter; `inln()`
  leaves the trailing line ending in place, whereas `in()` strips it.

You can use VT100 style %-codes in the output strings to set text color, text
weight, background color, and other display characteristics. See the
[VT100 cheat sheet](#vt100-cheat-sheet) below.

```php
<?php
// print to stdout
$stdio->outln('This is normal text.');

// print to stderr
$stdio->errln('%rThis is an error in red.%n');
?>
```

## The Getopt Object

The _Getopt_ object is separate from the _Context_. Use a _Getopt_ object to
parse the command-line `$_SERVER['argv']` values into options and arguments.

### Instantiation

Instantiation is straightforward:

```php
<?php
use Aura\Cli\Getopt;

$getopt = new Getopt;
?>
```

### Options and Params

To define command line options for _Getopt_ to recognize, use the
`setOptDefs()` method. This method uses a format similar to, but not exactly
the same as, the [getopt()](http://php.net/getopt) function in PHP. Instead of
defining short flags in a string and long options in a separate array, they
are both defined as elements in a single array.

```php
<?php
$getopt->setOptDefs([
    'a',     // short flag -a, parameter is not allowed
    'b:',    // short flag -b, parameter is required
    'c::',   // short flag -c, parameter is optional
    'foo',   // long option --foo, parameter is not allowed
    'bar:',  // long option --bar, parameter is required
    'baz::', // long option --baz, parameter is optional
]);
?>
```

Then `parse()` the `$_SERVER['argv']` values and `get()` the option values.

```php
<?php
$getopt->parse($context->server->get('argv', []));

$a   = $getopt->get('-a', 0); // 1 if -a was passed, 0 if not
$b   = $getopt->get('-b');
$c   = $getopt->get('-c', 'default value');
$foo = $getopt->get('--foo', 0); // 1 if --foo was passed, 0 if not
$bar = $getopt->get('--bar');
$baz = $getopt->get('--baz', 'default value');
?>
```

If you want a short flag and a long option to be mapped to the same long
option name, pass the short flag as an array key and the long option name as
its value:

```php
<?php
// map -f to --foo
$getopt->setOptDefs([
    'f:' => 'foo',  // short flag -f, parameter required
    'foo:',         // long option --foo, parameter required
]);

$name = $getopt->get('--foo'); // both -f and --foo map to '--foo'
?>
```

If an option is passed multiple times, it will result in an array of multiple
values. Options that do not take parameters will result in a count of how many
times the option was passed.

```php
<?php
// if the script was invoked with:
// php script.php --foo=foo --foo=bar --foo=baz -f -f -f
$getopt->setOptDefs(['f', 'foo:']);
$getopt->parse();
$foo = $getopt->get('--foo'); // ['foo', 'bar', 'baz']
$f_count = $getopt->get('-f'); // 3
?>
```

If you define options with the `setOptDefs()` method, and the user passes
options that do not conform to the definitions, the _Getopt_ object will track
various error messages related to the parsing failures.  In these cases,
`parse()` will return `false`, and you can then review the error messages.

```php
<?php
$success = $getopt->parse($context->server->get('argv', []));
if (! $success) {
    $errors = $getopt->getErrors();
    foreach ($errors as $error) {
        $stdio->errln($error);
    }
};
?>
```

### Positional Arguments

To get the positional arguments passed to the command line, use the `get()`
method and the argument position number:

```php
<?php
// if the script was invoked with:
// php script.php arg1 arg2 arg3 arg4

$val0 = $getopt->get(0); // script.php
$val1 = $getopt->get(1); // arg1
$val2 = $getopt->get(2); // arg2
$val3 = $getopt->get(3); // arg3
$val4 = $getopt->get(4); // arg4
?>
```

If you have defined options with `setOptDefs()`, options will be removed from the
arguments automatically.

```php
<?php
$getopt->setOptDefs([
    'a',
    'foo:',
]);

// if the script was invoked with:
// php script.php arg1 --foo=bar -a arg2
$arg0 = $getopt->get(0); // script.php
$arg1 = $getopt->get(1); // arg1
$arg2 = $getopt->get(2); // arg2
$foo  = $getopt->get('--foo'); // bar
$a    = $getopt->get('-a'); // 1
?>
```

Be careful; if an short flag has an optional parameter, it will be treated as
the flag value, not an argument.

```php
<?php
// if the script was invoked with:
// php script.php foo -a bar baz

// -a parameter is not allowed
$getopt->setOptDefs(['a']);
$arg0 = $getopt->get(0);    // script.php
$arg1 = $getopt->get(1);    // foo
$arg2 = $getopt->get(2);    // bar
$arg3 = $getopt->get(3);    // baz
$a    = $getopt->get('a');  // true

// -a parameter is required
$getopt->setOptDefs(['a']);
$arg0 = $getopt->get(0);    // script.php
$arg1 = $getopt->get(1);    // foo
$arg2 = $getopt->get(2);    // baz
$a    = $getopt->get('a');  // bar

// -a parameter is optional
$getopt->setOptDefs(['a']);
$arg0 = $getopt->get(0);    // script.php
$arg1 = $getopt->get(1);    // foo
$arg2 = $getopt->get(2);    // baz
$a    = $getopt->get('a');  // bar
?>
```

### Named Arguments

To set names on positional arguments, call `setArgDefs()` with an array where
the key is the argument position and the value is the argument name you would
like to use.

```php
<?php
// set the option definitions
$getopt->setOptDefs([
    'a',
    'foo:',
]);

// set the names for argument positions
$getopt->setArgDefs([
    0 => 'script_name',
    1 => 'first_arg',
    2 => 'second_arg',
]);

// if the script was invoked with:
// php script.php arg1 --foo=bar -a arg2
$arg0 = $getopt->get('script_name');    // script.php
$arg1 = $getopt->get('first_arg');      // arg1
$arg2 = $getopt->get('second_arg');     // arg2
$foo  = $getopt->get('--foo');          // bar
$a    = $getopt->get('-a');             // 1
?>
```


## VT100 Cheat Sheet

Insert these VT100 %-codes into stdout or stderr text strings to get the
related display behaviors.

Text color, normal weight:

    %k      black
    %r      red
    %g      green
    %y      yellow
    %b      blue
    %m      magenta/purple
    %p      magenta/purple
    %c      cyan/light blue
    %w      white
    %n      reset to terminal default

Text color, bold weight:

    %K      black
    %R      red
    %G      green
    %Y      yellow
    %B      blue
    %M      magenta/purple
    %P      magenta/purple
    %C      cyan/light blue
    %W      white
    %N      terminal default

Background color:

    %0      black background
    %1      red background
    %2      green background
    %3      yellow background
    %4      blue background
    %5      magenta/purple background
    %6      cyan/light blue background
    %7      white background

Assorted style shortcuts:

    %F      blink/flash
    %_      blink/flash
    %U      underline
    %I      reverse/inverse
    %*      bold
    %d      dim
    %%      literal percent sign
