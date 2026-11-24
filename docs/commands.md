# Writing Commands

## Exit Codes

This library comes with a _Status_ class that defines constants for exit
status codes. You should use these whenever possible.  For example, if a
command is used with the wrong number of arguments or improper option flags,
`exit()` with `Status::USAGE`.  The exit status codes are the same as those
found in [sysexits.h](http://www.unix.com/man-page/freebsd/3/sysexits/).

## A Standalone Command

The Aura.Cli library does not come with an abstract or base command class to
extend from, but writing commands for yourself is straightforward. The
following is a standalone command script, but similar logic can be used in a
class.  Save it in a file named `hello` and invoke it with
`php hello [-v,--verbose] [name]`.

```php
<?php
use Aura\Cli\CliFactory;
use Aura\Cli\Status;

require __DIR__ . '/vendor/autoload.php';

// get the context and stdio objects
$cli_factory = new CliFactory;
$context = $cli_factory->newContext($GLOBALS);
$stdio = $cli_factory->newStdio();

// define options and named arguments through getopt
$options = ['verbose,v'];
$getopt = $context->getopt($options);

// were the options passed correctly?
if ($getopt->hasErrors()) {
    foreach ($getopt->getErrors() as $error) {
        $stdio->errln($error->getMessage());
    }
    exit(Status::USAGE);
}

// do we have a name to say hello to?
$name = $getopt->get(1);
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

## Showing Progress

For a command that works through a list of items, use a
[_ProgressBar_](stdio.md#progress-bar) to show how far along it is. Save the
following in a file named `count-lines` and invoke it with
`php count-lines file1 [file2 ...]`.

```php
<?php
use Aura\Cli\CliFactory;
use Aura\Cli\Status;
use Aura\Cli\Stdio\ProgressBar;

require __DIR__ . '/vendor/autoload.php';

$cli_factory = new CliFactory;
$context = $cli_factory->newContext($GLOBALS);
$stdio = $cli_factory->newStdio();

// all arguments after the script name are files
$files = array_slice($context->argv->get(), 1);
if (! $files) {
    $stdio->errln("Please give at least one file.");
    exit(Status::USAGE);
}

$lines = 0;
$progress = new ProgressBar($stdio, count($files));
$progress->start();
foreach ($files as $file) {
    $lines += count(file($file));
    $progress->advance();
}
$progress->finish();

// print the result after finish(), so the bar does not overwrite it
$stdio->outln("{$lines} lines in " . count($files) . " files.");
exit(Status::SUCCESS);
?>
```
