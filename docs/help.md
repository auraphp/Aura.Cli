# Writing Command Help

Sometimes it will be useful to provide help output for your commands. With Aura.Cli, the _Help_ object is separate from any command you may write. It may be manipulated externally or extended.

For example, extend the _Help_ object and override the `init()` method.

```php
<?php
use Aura\Cli\Help;

class MyCommandHelp extends Help
{
    protected function init()
    {
        $this->setSummary('A single-line summary.');
        $this->setUsage('<arg1> [<arg2>]');
        $this->setOptions([
            'f,foo' => "The -f/--foo option description.",
            'bar::' => "The --bar option description.",
        ]);
        $this->setDescr("A multi-line description of the command.");
    }
}
?>
```

Then instantiate the new class and pass its `getHelp()` output through _Stdio_:

```php
<?php
use Aura\Cli\CliFactory;
use Aura\Cli\Context\OptionFactory;

$cli_factory = new CliFactory;
$stdio = $cli_factory->newStdio();

$help = new MyCommandHelp(new OptionFactory);
$stdio->outln($help->getHelp('my-command'));
?>
```


> - We keep the command name itself outside of the help class, because the command name may be mapped differently in different projects.
>
> - We pass a _GetoptParser_ to the _Help_ object so it can parse the option definitions.
>
> - We can get the option definitions out of the _Help_ object using `getOptions()`; this allows us to pass a _Help_ object into a hypothetical command object and reuse the definitions.

The output will look something like this:

```
SUMMARY
    my-command -- A single-line summary.

USAGE
    my-command <arg1> [<arg2>]

DESCRIPTION
    A multi-line description of the command.

OPTIONS
    -f
    --foo
        The -f/--foo option description.

    --bar[=<value>]
        The --bar option description.
```

As a side note, the array of options passed to `setOptions()` may contain argument descriptions as well. These are in the format `#argname` (to indicate a required argument) and `#argname?` (to indicate an optional argument). They may additionally be used as keys, with corresponding description values. Their presence in a _Getopt_ definition array is ignored, but the _Help_ object will read them and generate output for them automatically.

For example, the following code (notice the lack of a `setUsage()` call)...

```php
<?php
use Aura\Cli\CliFactory;
use Aura\Cli\Context\OptionFactory;
use Aura\Cli\Help;

$cli_factory = new CliFactory;
$stdio = $cli_factory->newStdio();

$help = new Help(new OptionFactory);
$help->setSummary('A single-line summary.');
$help->setOptions([
    'f,foo' => "The -f/--foo option description.",
    'bar::' => "The --bar option description.",
    '#arg1' => "The description for argument 1.",
    '#arg2?' => "The description for argument 2.",
]);
$help->setDescr("A multi-line description of the command.");

$stdio->outln($help->getHelp('my-command'));
?>
```

... will generate the following output:

```
SUMMARY
    my-command -- A single-line summary.

USAGE
    my-command <arg1> [<arg2>]

DESCRIPTION
    A multi-line description of the command.

ARGUMENTS
    <arg1>
        The description for argument 1.

    <arg2>
        The description for argument 2.

OPTIONS
    -f
    --foo
        The -f/--foo option description.

    --bar[=<value>]
        The --bar option description.
```

You can also append custom sections, such as EXAMPLES or SEE ALSO, with
`addSection()`. They appear after the options, in the order they were added.
The text may be a string or an array of lines; adding a section with an
existing title replaces it.

```php
<?php
$help->addSection('EXAMPLES', [
    'my-command foo',
    'my-command foo bar',
]);
$help->addSection('SEE ALSO', 'other-command(1)');
?>
```

... will append the following to the help output:

```
EXAMPLES
    my-command foo
    my-command foo bar

SEE ALSO
    other-command(1)
```
