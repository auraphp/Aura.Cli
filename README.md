# Aura.Cli

Provides the equivalent of request ( _Context_ ) and response ( _Stdio_ )
objects for the command line interface, including _Getopt_ support.

## Foreword

### Requirements

This library requires PHP 5.3 or later, and has no userland dependencies.

### Installation

This library is installable and autoloadable via Composer with the following
`require` element in your `composer.json` file:

    "require": {
        "aura/cli": "2.*@dev"
    }
    
Alternatively, download or clone this repository, then require or include its
_autoload.php_ file.

### Tests

[![Build Status](https://travis-ci.org/auraphp/Aura.Cli.png?branch=develop-2)](https://travis-ci.org/auraphp/Aura.Cli)

This library has 100% code coverage with [PHPUnit][]. To run the tests at the
command line, go to the _tests_ directory and issue `phpunit`.

[phpunit]: http://phpunit.de/manual/

### PSR Compliance

This library attempts to comply with [PSR-1][], [PSR-2][], and [PSR-4][]. If
you notice compliance oversights, please send a patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md


## Getting Started

### Context Discovery

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
properties are copies of those superglobals as they were are at the time of
_Context_ instantiation.) You can pass an alternative default value if the
related key is missing.

```php
<?php
// get copies of superglobals
$env    = $context->env->get();
$server = $context->server->get();
$argv   = $context->argv->get();

// equivalent to:
// $value = isset($_ENV['key']) ? $_ENV['key'] : null;
$value = $context->env->get('key');

// equivalent to:
// $value = isset($_ENV['key']) ? $_ENV['key'] : 'other_value';
$value = $context->env->get('key', 'other_value');
?>
```

### Getopt Support

The _Context_ object provides support for retrieving command-line options and
params, along with positional and named arguments.

To retrieve options and arguments parsed from the command-line `$argv` values,
use the `getopt()` method on the _Context_ object. This will return a
_GetoptValues_ object for you to use as as you wish.

#### Defining Options and Params

To tell `getopt()` how to recognize command line options, pass an array of
option definitions. The definitions array format is similar to, but not
exactly the same as, the one used by the [getopt()](http://php.net/getopt)
function in PHP. Instead of defining short flags in a string and long options
in a separate array, they are both defined as elements in a single array.
Adding a `*` after the option name indicates it can be passed multiple times;
its values will be stored in an array.

```php
<?php
$options = array(
    'a',        // short flag -a, parameter is not allowed
    'b:',       // short flag -b, parameter is required
    'c::',      // short flag -c, parameter is optional
    'foo',      // long option --foo, parameter is not allowed
    'bar:',     // long option --bar, parameter is required
    'baz::',    // long option --baz, parameter is optional
    'g*::',     // short flag -g, parameter is optional, multi-pass
);

$getopt = $context->getopt($options);
?>
```

Then use the `get()` method on the returned _GetoptValues_ object to retrieve
the option values; you can provide an alternative default value for when the
option is missing.

```php
<?php
$a   = $getopt->get('-a', false); // true if -a was passed, false if not
$b   = $getopt->get('-b');
$c   = $getopt->get('-c', 'default value');
$foo = $getopt->get('--foo', 0); // true if --foo was passed, false if not
$bar = $getopt->get('--bar');
$baz = $getopt->get('--baz', 'default value');
$g   = $getopt->get('-g', []);
?>
```

If you want alias one option name to another, comma-separate the two names.
The values will be stored under both names;

```php
<?php
// alias -f to --foo
$options = array(
    'foo,f:',  // long option --foo or short flag -f, parameter required
);

$getopt = $context->getopt($options);

$foo = $getopt->get('--foo'); // both -f and --foo have the same values
$f   = $getopt->get('-f'); // both -f and --foo have the same values
?>
```

If an option is passed multiple times, it will result in an array of multiple
values.

```php
<?php
$options = array(
    'f',
    'foo:'
);

$getopt = $context->getopt($options);

// if the script was invoked with:
// php script.php --foo=foo --foo=bar --foo=baz -f -f -f
$foo = $getopt->get('--foo'); // ['foo', 'bar', 'baz']
$f   = $getopt->get('-f'); // [true, true, true]
?>
```

If the user passes options that do not conform to the definitions, the
_GetoptValues_ object retains various errors related to the parsing
failures. In these cases, `hasErrors()` will return `true`, and you can then
review the errors.  (The errors are actually `Aura\Cli\Exception` objects,
but they don't get thrown as they occur; this is so that you can deal with or
ignore the different kinds of errors as you like.)

```php
<?php
$getopt = $context->getopt($options);
if ($getopt->hasErrors()) {
    $errors = $getopt->getErrors();
    foreach ($errors as $error) {
        // print error messages to stderr using a Stdio object
        $stdio->errln($error->getMessage());
    }
};
?>
```

#### Positional Arguments

To get the positional arguments passed to the command line, use the `get()`
method and the argument position number:

```php
<?php
$getopt = $context->getopt();

// if the script was invoked with:
// php script.php arg1 arg2 arg3 arg4

$val0 = $getopt->get(0); // script.php
$val1 = $getopt->get(1); // arg1
$val2 = $getopt->get(2); // arg2
$val3 = $getopt->get(3); // arg3
$val4 = $getopt->get(4); // arg4
?>
```

Defined options will be removed from the arguments automatically.

```php
<?php
$options = array(
    'a',
    'foo:',
);

$getopt = $context->getopt($options);

// if the script was invoked with:
// php script.php arg1 --foo=bar -a arg2
$arg0 = $getopt->get(0); // script.php
$arg1 = $getopt->get(1); // arg1
$arg2 = $getopt->get(2); // arg2
$foo  = $getopt->get('--foo'); // bar
$a    = $getopt->get('-a'); // 1
?>
```

> N.b.: If an short flag has an optional parameter, the argument immediately
> after it will it will be treated as the option value, not as an argument.


### Standard Input/Output Streams

The _Stdio_ object to allows you to work with standard input/output streams.
(This is the command line equivalent of a web response object.)

Instantiate a _Stdio_ object using the _CliFactory_.

```php
<?php
use Aura\Cli\CliFactory;

$cli_factory = new CliFactory;
$stdio = $cli_factory->newStdio();
?>
```

It defaults to using `php://stdin`, `php://stdout`, and `php://stderr`, but
you can pass whatever stream names you like as parameters to the `newStdio()`
method.

The _Stdio_ object methods are ...

- `getStdin()`, `getStdout()`, and `getStderr()` to return the respective
  _Handle_ objects;

- `outln()` and `out()` to print to _stdout_, with or without a line ending;

- `errln()` and `err()` to print to _stderr_, with or without a line ending;

- `inln()` and `in()` to read from _stdin_ until the user hits enter; `inln()`
  leaves the trailing line ending in place, whereas `in()` strips it.

You can use special formatting markup in the output and error strings to set
text color, text weight, background color, and other display characteristics.
See the [formatter cheat sheet](#formatter-cheat-sheet) below.

```php
<?php
// print to stdout
$stdio->outln('This is normal text.');

// print to stderr
$stdio->errln('<<red>>This is an error in red.');
$stdio->errln('Output will stay red until a formatting change.<<reset>>');
?>
```

### Exit Codes

This library comes with a _Status_ class that defines constants for exit
status codes. You should use these whenever possible.  For example, if a
command is used with the wrong number of arguments or improper option flags,
`exit()` with `Status::USAGE`.  The exit status codes are the same as those
found in [sysexits.h](http://www.unix.com/man-page/freebsd/3/sysexits/).

### Writing Commands

The Aura.Cli library does not come with an abstract or base command class to
extend from, but writing commands for yourself is straightforward. The
following is a standalone command script, but similar logic can be used in a
class.  Save it in a file named `hello` and invoke it with
`php hello [-v,--verbose] [name]`.

```php
<?php
use Aura\Cli\CliFactory;
use Aura\Cli\Status;

require '/path/to/Aura.Cli/autoload.php';

// get the context and stdio objects
$cli_factory = new CliFactory;
$context = $cli_factory->newContext($GLOBALS);
$stdio = $cli_factory->newStdio();

// define options and named arguments through getopt
$options = ['verbose,v'];
$getopt = $context->getopt($options);

// do we have a name to say hello to?
$name = $getopt->get(0);
if (! $name) {
    // print an error
    $stdio->errln("Please give a name to say hello to.");
    exit(Status::USAGE);
}

// say hello
if ($getopt->get('--verbose')) {
    // verbose output
    $stdio->outln("Hello {$name}, it's nice to see you!");
} else {
    // plain output
    $stdio->outln("Hello {$name}!");
}

// done!
exit(Status::SUCCESS);
?>
```

### Formatter Cheat Sheet

On POSIX terminals, `<<markup>>` strings will change the display
characteristics. Note that these are not HTML tags; they will be converted
into terminal control codes, and do not get "closed". You can place as many
space-separated markup codes between the double angle-brackets as you like.

    reset       reset display to defaults
    
    black       black text
    red         red text
    green       green text
    yellow      yellow text
    blue        blue text
    magenta     magenta (purple) text
    cyan        cyan (light blue) text
    white       white text
    
    blackbg     black background
    redbg       red background
    greenbg     green background
    yellowbg    yellow background
    bluebg      blue background
    magentabg   magenta (purple) background
    cyanbg      cyan (light blue) background
    whitebg     white background

    bold        bold in the current text and background colors
    dim         dim in the current text and background colors
    ul          underline in the current text and background colors
    blink       blinking in the current text and background colors
    reverse     reverse the current text and background colors

For example, to set bold white text on a red background, add `<<bold white redbg>>`
into your output or error string. Reset back to normal with `<<reset>>`.
