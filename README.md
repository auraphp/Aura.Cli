Aura CLI
========

The Aura CLI package provides a system for creating and executing CLI command
objects. It includes facilities for parsing command-line options and handling
standard input/output.

This package is compliant with [PSR-0][], [PSR-1][], and [PSR-2][]. If you
notice compliance oversights, please send a patch via pull request.

[PSR-0]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md


Basic Usage
===========

(N.b.: The CLI package is one of the most complex packages in the Aura
project.)


Instantiation and Execution
---------------------------

Most Aura packages allow you to instantiate an object by including a
particular file. This is not the case with Aura CLI. Because commands are so
specific to the logic of your particular needs, you will have to extend the
`AbstractCommand` class yourself and add an action method for your own
purposes.

First, either include the `'Aura.Cli/src.php'` file to load the package
classes, or add the `'Aura.Cli/src/'` directory to your autoloader.

Next, create a command class of your own, extending the `AbstractCommand`
class:

    <?php
    namespace Vendor\Package\Cli;
    use Aura\Cli\AbstractCommand;
    class ExampleCommand extends AbstractCommand
    {
        public function action()
        {
            $this->stdio->outln('Hello World!');
        }
    }

Instantiating and executing the command class is moderately complex; it needs
several dependency objects, all provided by the Aura CLI package.

    <?php
    namespace Vendor\Package\Cli;
    use Aura\Cli\Context;
    use Aura\Cli\Getopt;
    use Aura\Cli\OptionFactory;
    use Aura\Cli\Stdio;
    use Aura\Cli\Vt100;
    
    // instantiate
    $command = new ExampleCommand(
        new Context($GLOBALS),
        new Stdio(
            fopen('php://stdin', 'r'),
            fopen('php://stdout', 'w+'),
            fopen('php://stderr', 'w+'),
            new Vt100
        ),
        new Getopt(new OptionFactory)
    );
    
    // execute
    $command->exec();

(If you have a dependency injection mechanism, you can automate the the
creation and injection of the dependency objects. The
[Aura.Di](https://github.com/auraphp/Aura.Di) package is one such system.)

Save the file as `command.php`, then invoke it like so:

    php command.php
    
You should see a `Hello World!` message.


Action and Input/Output
-----------------------

The logic for the command goes in the `action()` method. In the example below,
we perform some basic input/output.

    <?php
    namespace Vendor\Package;
    use Aura\Cli\AbstractCommand;

    class ExampleCommand extends AbstractCommand
    {
        public function action()
        {
            $this->stdio->outln('Hello World!');
            $this->stdio->out('Please enter some text: ');
            $input = $this->stdio->in();
            $this->stdio->errln('Input was ' . $input);
        }
    }

Use the `$stdio` object to work with standard input/output streams. Its
methods are:

- `outln()` and `out()`: Print to stdout, with or without a line ending.

- `errln()` and `err()`: Print to stderr, with or without a line ending.

- `inln()` and `in()`: Read from stdin until the user hits enter; `inln()` leaves the trailing line ending in place, whereas `in()` strips it.


Method Hooks
------------

There are four method hooks on the CLI command. Use the pre- and post-action
methods to perform logic before and after the action; use pre- and post-exec
methods to perform setup and teardown.

    <?php
    namespace Vendor\Package;
    use Aura\Cli\AbstractCommand;
    
    class ExampleCommand extends AbstractCommand
    {
        protected $input = 'foo bar baz';
        
        public function preExec()
        {
            // perform object setup here
        }
        
        public function preAction()
        {
            $this->stdio->outln('The input is currently ' . $this->input);
        }
        
        public function action()
        {
            $this->stdio->out('Please enter some text: ');
            $this->input = $this->stdio->in();
        }
        
        public function postAction()
        {
            $this->stdio->outln('The input was %r%2' . $this->input . '%n');
        }
        
        public function postExec()
        {
            // perform object teardown here
        }
    }

Notice in `postAction()` that we set the background and foreground text
colors. For more information, please examine the [Vt100 format
codes](https://github.com/auraphp/Aura.Cli/blob/master/src/Aura/Cli/Vt100.php).


Argument Params
---------------

We may wish to pass information as part of the invocation. To read this
information while in the command, use `$this->params`.

    <?php
    namespace Vendor\Package;
    use Aura\Cli\AbstractCommand;

    class ExampleCommand extends AbstractCommand
    {
        public function action()
        {
            foreach ($this->params as $key => $val) {
                $this->stdio->outln("Param $key is '$val'.");
            }
        }
    }
    
For example, if we issue ...
    
    $ php command.php foo bar baz

... then the `action()` will output:

    Param 0 is 'command.php'
    Param 1 is 'foo'.
    Param 2 is 'bar'.
    Param 3 is 'baz'.


Advanced Usage
==============

Long And Short Options
----------------------

In addition to argument params, we may wish to pass short or long options as
part of the invocation. These are the `-o` and `--option` portions of the
invocation.

To work with options, we first define them in the `$options` array of the
command. Then we can retrieve the option values through the `$getopt` object.

To define an option, do something like the following:

    <?php
    namespace Vendor\Package;
    use Aura\Cli\AbstractCommand;
    use Aura\Cli\Option;
    
    class ExampleCommand extends AbstractCommand
    {
        protected $options = [
            'foo_bar' => [
                'long'    => 'foo-bar',
                'short'   => 'f',
                'param'   => Option::PARAM_REQUIRED,
                'multi'   => false,
                'default' => null,
            ],
        ];
        
        public function action()
        {
            $this->stdio->out("The value of -f/--foo-bar is ");
            $this->stdio->outln($this->getopt->foo_bar);
        }
    }

When we invoke the above command like this ...

    $ php command.php --foo-bar=gir

... it will print this output:

    The value of -f/--foo-bar is gir.

The `$options` array is keyed on what we want as the option name, and each
element is an array of option definition keys:

- `'long'`: The long form of the option, which is passed by prefixing it with two dashes at the command line.  A long-form param value is passed by following it with an equals sign and the value; e.g., `--foo-bar=some_value`. Leave this empty if we do not want a long-form option.

- `'short'`: The short form of the option, which is passed by prefixing it with one dash at the command line.  A short-form param value is passed by following it with a space and the value; e.g., `-f some_value`. Leave this empty if we do not want a short-form option.

- `'param'`: Is a a param value required for the option, is it optional, or is it disallowed?  Use `Option::PARAM_REQUIRED` to force a param value to be passed, `Option::PARAM_OPTIONAL` to allow a value to be passed or not, or `Option::PARAM_REJECTED` to disallow any value from being passed.

- `'multi'`: Is the option allowed to be passed multiple times in the same command?  E.g., "-f foo -f bar -f zim" will make the option value an array with three entries: `['foo'`, `'bar'`, `'zim']`.

- `'default'`: The default value for the option if it is not passed.

After we have defined the options and passed them at the command line, we can
read them from the `$getopt` object as magic read-only properties. Thus, for
the above option named as `'foo_bar'`, we can retrieve its value by using
`$this->getopt->foo_bar`.
