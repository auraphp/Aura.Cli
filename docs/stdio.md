# Standard Input/Output Streams

The _Stdio_ object allows you to work with standard input/output streams.
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

## Formatter Cheat Sheet

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
