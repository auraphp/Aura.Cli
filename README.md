Aura CLI
========

The Aura CLI package provides a system for creating and executing CLI command objects.  It includes facilities for parsing command-line options and handling standard input/output.

Basic Usage
===========

Action and Input/Output
-----------------------

The logic for the command goes in the `action()` method. In the example below, we perform some basic input/output.

    <?php
    namespace Vendor\Package;
    use Aura\Cli\AbstractCommand;

    class Example extends AbstractCommand
    {
        public function action()
        {
            $this->stdio->outln('Hello World!');
            $this->stdio->out('Please enter some text: ');
            $input = $this->stdio->in();
            $this->stdio->errln('Input was ' . $input);
        }
    }

When we invoke that command, it will output "Hello World!", ask for some input, and then print that input to the error stream.

Use the `$stdio` object to work with standard input/output streams.  Its methods are:

- `outln()` and `out()`: Print to stdout, with or without a line ending.

- `errln()` and `err()`: Print to stderr, with or without a line ending.

- `inln()` and `in()`: Read from stdin until the user hits enter; `inln()` leaves the trailing line ending in place, whereas `in()` strips it.


Method Hooks
------------

There are four method hooks on the CLI command.  Use the pre- and post-action methods to perform logic before and after the action; use pre- and post-exec methods to perform setup and teardown.

    <?php
    namespace Vendor\Package;
    use Aura\Cli\AbstractCommand;
    
    class Example extends AbstractCommand
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
            $this->stdio->outln('The input was ' . $this->input);
        }
        
        public function postExec()
        {
            // perform object teardown here
        }
    }


Argument Params
---------------

We may wish to pass information as part of the invocation.  To read this information while in the command, use `$this->params`.

    <?php
    namespace Vendor\Package;
    use Aura\Cli\AbstractCommand;

    class Example extends AbstractCommand
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

    Param 0 is 'foo'.
    Param 1 is 'bar'.
    Param 2 is 'baz'.


Advanced Usage
==============

Long And Short Options
----------------------

In addition to argument params, we may wish to pass certain short switches or long options as part of the invocation.  These are the `-a` and `--option=value` portions of the invocation.

To work with options, we first define them in the `$options` array of the command.  Then we can retrieve the option values through the `$getopt` object.

To define an option, do something like the following:

    <?php
    namespace Vendor\Package;
    use Aura\Cli\AbstractCommand;
    use Aura\Cli\Option;
    
    class Example extends AbstractCommand
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
            $this->stdio->out("The value of -f/--foo-bar is ")
            $this->stdio->outln($this->getopt->foo_bar);
        }
    }

When we invoke the above command like this ...

    $ php command.php --foo-bar=gir

... it will print this output:

    The value of -f/--foo-bar is gir.

The `$options` array is keyed on what we want as the option name, and each element is an array of option definition keys:

- `'long'`: The long form of the option, which is passed by prefixing it with two dashes at the command line.  A long-form param value is passed by following it with an equals sign and the value; e.g., `--foo-bar=some_value`. Leave this empty if we do not want a long-form option.

- `'short'`: The short form of the option, which is passed by prefixing it with one dash at the command line.  A short-form param value is passed by following it with a space and the value; e.g., `-f some_value`. Leave this empty if we do not want a short-form option.

- `'param'`: Is a a param value required for the option, is it optional, or is it disallowed?  Use `Option::PARAM_REQUIRED` to force a param value to be passed, `Option::PARAM_OPTIONAL` to allow a value to be passed or not, or `Option::PARAM_REJECTED` to disallow any value from being passed.

- `'multi'`: Is the option allowed to be passed multiple times in the same command?  E.g., "-f foo -f bar -f zim" will make the option value an array with three entries: `['foo'`, `'bar'`, `'zim']`.

- `'default'`: The default value for the option if it is not passed.

After we have defined the options and passed them at the command line, we can read them from the `$getopt` object as magic read-only properties.  Thus, for the above option named as `'foo_bar'`, we can retrieve its value by using `$this->getopt->foo_bar`.
