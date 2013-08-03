Aura.Cli
========

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
[PHPUnit][], then go to the library _tests_ directory
and issue `phpunit` at the command line.

[PHPUnit]: http://phpunit.de/manual/

### API Documentation

This library has embedded DocBlock API documentation. To generate the
documentation in HTML, first install [PHPDocumentor][], then go to the library
directory and issue the following at the command line:

    phpdoc -d ./src -t /path/to/output --force

You can then browse the HTML-formatted API documentation at _/path/to/output_.
    
[PHPDocumentor]: http://phpdoc.org/docs/latest/for-users/installation.html

### PSR Compliance

This library is compliant with [PSR-1][] and [PSR-2][]. If you notice
compliance oversights, please send a patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md


The Context Object
------------------

The _Context_ object provides information about the command line environment,
including any option flags passed via the command line. (This is the command
line equivalent of a web request object.)

Instantiate a _Context_ object like so:

```php
<?php
use Aura\Cli\Context;

$context = new Context(new Context\PropertyFactory($GLOBALS));
?>
```

### Environment And Server Values

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

### Command Line Options

To define command line options for the _Context_ to recognize, use the
`getopt()` method. This method uses a format similar to, but not exactly the
same as, the [getopt()](http://php.net/getopt) function in PHP.

Instead of defining short flags in a string and long options in a separate
array, they are defined as elements in a single array. Once these are defined,
you can use the `$opts` property to get the option values.

```php
<?php
$context->getopt([
    'a',        // short flag -a, parameter is not allowed
    'b:',       // short flag -b, parameter is required
    'c::',      // short flag -c, parameter is optional
    'foo',      // long option --foo, parameter is not allowed
    'bar:',     // long option --bar, parameter is required
    'baz::',    // long option --baz, parameter is optional
]);

$a   = $context->opts->get('a', false); // true if -a was passed, false if not
$b   = $context->opts->get('b');
$c   = $context->opts->get('c', 'default value');
$foo = $context->opts->get('foo', false); // true if --foo was passed, false if not
$bar = $context->opts->get('bar');
$baz = $context->opts->get('baz', 'default value');
?>
```

If you want a short flag and a long option to be mapped to the same name, pass
the definition as an array key and the name as the array value:

```php
<?php
// map -f and --foo to 'name'
$context->getopt([
    'f:'   => 'name',   // short flag -f, parameter required
    'foo:' => 'name',   // long option --foo, parameter required
]);

$name = $context->opts->get('name'); // both -f and --foo map to 'name'
?>
```

If an option is passed multiple times, it will result in an array of multiple
values.

```php
<?php
// if the script was invoked with:
// php script.php -f foo -f bar -f baz
$context->getopt(['f:']);
$values = $context->opts->get('f'); // ['foo', 'bar', 'baz']
?>
```

If you define options with the `getopt()` method, and the user passes options
that do not conform to the definitions, the _Getopt_ object will throw various
exceptions:

- _OptionNotDefined_ if the user passed an option that was not defined;

- _OptionParamRejected_ if the user specified a parameter on an option where a
  parameter is not allowed; and

- _OptionParamRequired_ if the user did not specify a parameter on an option
  that requires one

### Command Line Arguments

To get the positional arguments passed to the command line, use the `$argv`
property:

```php
<?php
// if the script was invoked with:
// php script.php arg1 arg2 arg3 arg4

$val0 = $context->argv->get(0); // script.php
$val1 = $context->argv->get(1); // arg1
$val2 = $context->argv->get(2); // arg2
$val3 = $context->argv->get(3); // arg3
$val4 = $context->argv->get(4); // arg4
?>
```

If you have defined options with `getopt()`, options will be removed from the
arguments automatically.

```php
<?php
// if the script was invoked with:
// php script.php arg1 --foo=bar -a arg2

$context->getopt([
    'a',
    'foo:',
]);

$val0 = $context->argv->get(0); // script.php
$val1 = $context->argv->get(1); // arg1
$val2 = $context->argv->get(2); // arg2
?>
```

Be careful; if an short flag has an optional parameter, it will be treated as
the flag value, not an argument.

```php
<?php
// if the script was invoked with:
// php script.php foo -a bar baz

// -a parameter is not allowed
$context->getopt(['a']);
$arg0 = $context->argv->get(0); // script.php
$arg1 = $context->argv->get(1); // foo
$arg2 = $context->argv->get(2); // bar
$arg3 = $context->argv->get(3); // baz
$a    = $context->opts->get('a'); // true

// -a parameter is required
$context->getopt(['a']);
$arg0 = $context->argv->get(0); // script.php
$arg1 = $context->argv->get(1); // foo
$arg2 = $context->argv->get(2); // baz
$a    = $context->opts->get('a'); // bar

// -a parameter is optional
$context->getopt(['a']);
$arg0 = $context->argv->get(0); // script.php
$arg1 = $context->argv->get(1); // foo
$arg2 = $context->argv->get(2); // baz
$a    = $context->opts->get('a'); // bar
?>
```

The Stdio Object
----------------

The _Stdio_ object to allows you to work with standard input/output streams.
(This is the command line equivalent of a web response object.)

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
reource handles.

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

VT100 Cheat Sheet
=================

(Insert these VT100 %-codes into stdout or stderr text strings to get the
related display behaviors.)

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
