# Getopt Support

The _Context_ object provides support for retrieving command-line options and
params, along with positional arguments.

To retrieve options and arguments parsed from the command-line `$argv` values,
use the `getopt()` method on the _Context_ object. This will return a
_GetoptValues_ object for you to use as as you wish.

## Defining Options and Params

To tell `getopt()` how to recognize command line options, pass an array of
option definitions. The definitions array format is similar to, but not
exactly the same as, the one used by the [getopt()](http://php.net/getopt)
function in PHP. Instead of defining short flags in a string and long options
in a separate array, they are both defined as elements in a single array.
Adding a `*` after the option name indicates it can be passed multiple times;
its values will be stored in an array.

```php
<?php
$options = [
    'a',        // short flag -a, parameter is not allowed
    'b:',       // short flag -b, parameter is required
    'c::',      // short flag -c, parameter is optional
    'foo',      // long option --foo, parameter is not allowed
    'bar:',     // long option --bar, parameter is required
    'baz::',    // long option --baz, parameter is optional
    'g*::',     // short flag -g, parameter is optional, multi-pass
];

$getopt = $context->getopt($options);
?>
```

> N.b.: When we say "required" here, it means "the option, when present,
> must have a parameter."  It does *not* mean "the option must be present."
> These are options, after all. If a particular value *must* be passed,
> consider using [positional arguments](#positional-arguments) instead.

Use the `get()` method on the returned _GetoptValues_ object to retrieve the
option values. You can provide an alternative default value for when the
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

If you want to alias one option name to another, comma-separate the two names.
The values will be stored under both names;

```php
<?php
// alias -f to --foo
$options = [
    'foo,f:',  // long option --foo or short flag -f, parameter required
];

$getopt = $context->getopt($options);

$foo = $getopt->get('--foo'); // both -f and --foo have the same values
$f   = $getopt->get('-f'); // both -f and --foo have the same values
?>
```

If you want to allow an option to be passed multiple times, add a '*' to the end
of the option name.

```php
<?php
$options = [
    'f*',
    'foo*:'
];

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

## Positional Arguments

To get the positional arguments passed to the command line, use the `get()`
method and the argument position number:

```php
<?php
$getopt = $context->getopt($options);

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
$options = [
    'a',
    'foo:',
];

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

> N.b.: If a short flag has an optional parameter, the argument immediately
> after it will be treated as the option value, not as an argument, unless it
> begins with `-`. If a short flag has a required parameter, the argument
> immediately after it is always treated as the value, even if it begins
> with `-`.

```php
<?php
$options = [
    'a',    // short flag -a, parameter is not allowed
    'b:',   // short flag -b, parameter is required
    'c::',  // short flag -c, parameter is optional
];

$getopt = $context->getopt($options);

// php script.php -b -5     => -b is '-5'
// php script.php -b 0      => -b is '0'
// php script.php -c foo    => -c is 'foo'
// php script.php -c -a     => -c is true, -a is true
// php script.php -c -5     => -c is true, and '-5' is an undefined option error
?>
```
