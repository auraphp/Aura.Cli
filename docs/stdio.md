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

## Progress Bar

The _ProgressBar_ object draws a progress bar on standard output for a task
with a known number of steps.

```php
<?php
use Aura\Cli\Stdio\ProgressBar;

$files = glob('/path/to/files/*');
$progress = new ProgressBar($stdio, count($files));

$progress->start();
foreach ($files as $file) {
    // ... process the file ...
    $progress->advance();
}
$progress->finish();
?>
```

While it runs, the bar is redrawn in place on a single line:

    [==========>                   ] 33%  10/30 remaining: 20 sec.  elapsed: 10 sec.

The _ProgressBar_ methods are ...

- `start()` to set the progress to zero, start the timer, and draw the bar;

- `advance($step = 1)` to move forward by `$step` steps;

- `setProgress($progress)` to set the number of steps done directly; the value
  is kept between zero and the total;

- `finish()` to draw the bar at 100% and end the line;

- `getProgress()` and `getTotal()` to return the steps done and the total;

- `render()` to return the bar line for the current state as a string,
  without printing it.

Calling `advance()` or `setProgress()` before `start()` starts the bar.
Calling `finish()` a second time does nothing until you call `start()` again,
which resets the bar and its timer. Each bar keeps its own start time, so you
can run several bars one after another.

The "remaining" estimate is based on the average time per step so far, and
shows `--` until the first step is done. A total of `0` draws a full bar.

### Width

The bar is 30 characters wide by default; pass a different width as the third
constructor argument:

```php
<?php
$progress = new ProgressBar($stdio, count($files), 50);
?>
```

A negative total, or a width less than 1, throws an
`InvalidArgumentException`.

### Terminals and Redirected Output

When standard output is not a POSIX terminal (for example, when it is piped to
a file or another program, or on Windows), the in-place redraws are skipped
and `finish()` prints only the final line, so logs are not filled with
carriage returns.

On a terminal the bar redraws its own line with a carriage return, so do not
print anything else to the terminal between `start()` and `finish()`; the
next redraw would overwrite it, or it would be printed in the middle of the
bar. Print messages before `start()` or after `finish()` instead.

### Testing

To test code that uses a progress bar, write to `php://memory` handles and pass
a clock as the fourth constructor argument. The clock is a callable that
returns the current time in seconds; it defaults to `microtime(true)`. Pass
`true` as the fourth _Handle_ argument to force terminal output, or `false`
to force redirected output.

```php
<?php
use Aura\Cli\Stdio;
use Aura\Cli\Stdio\Formatter;
use Aura\Cli\Stdio\Handle;
use Aura\Cli\Stdio\ProgressBar;

$stdout = new Handle('php://memory', 'w+', null, false);
$stdio = new Stdio(
    new Handle('php://memory', 'r'),
    $stdout,
    new Handle('php://memory', 'w+'),
    new Formatter
);

$now = 0;
$clock = function () use (&$now) {
    return $now;
};

$progress = new ProgressBar($stdio, 4, 10, $clock);
$progress->start();
$now = 8;
$progress->finish();

$stdout->rewind();
echo $stdout->fread();
// [==========] 100%  4/4 remaining: 0 sec.  elapsed: 8 sec.
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
