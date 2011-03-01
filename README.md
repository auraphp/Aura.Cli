Introduction
============

The Aura CLI package provides a system for creating and executing CLI `Command` objects.


Getting Started
===============

Create an Invoking Script
-------------------------

Before you can run a `Command` object, you need what we will call an invoking script.  It is the equivalent of a boostrap script in a web environment. The invoking script is what you run from the command line; it sets up the execution environment, creates the `Command` object and executes it.

For example, the following code will instantiate a `vendor\package\ExampleCommand` class and execute it.  This is a little long; it it provided as an example of the full manual setup for a `Command`. In regular use, you would provide these services via a dependency injector or other mechanism.

    <?php
    /** 
     * Populate and register your autoloader.
     */
    // ...
    
    /**
     * Classes we need to use.
     */
    use aura\cli\Context;
    use aura\cli\Stdio;
    use aura\cli\Getopt;
    use aura\signal\Manager;
    use aura\signal\HandlerFactory;
    use aura\signal\ResultFactory;
    use aura\signal\ResultCollection;
    
    /**
     * Dependency objects.
     */
    // create a cli context object for access to $_ENV, $_SERVER, etc.
    // remove the first argument from $argv (this is the name of the invoking
    // script)
    $context = new Context;
    $context->shiftArgv();
    
    // create a standard input/output object
    $stdio = new Stdio(
        fopen('php://stdin', 'r'),
        fopen('php://stdout', 'w+'),
        fopen('php://stderr', 'w+'),
        new Vt100
    );
    
    // create an object to parse options and values
    $getopt = new Getopt;
    
    // create a signal manager
    $signal = new Manager(
        new HandlerFactory,
        new ResultFactory,
        new ResultCollection
    );
    
    /**
     * Create an execute a command object.
     */
    $command = new vendor\package\Example(
        $context,
        $stdio,
        $getopt,
        $signal
    );
    
    $command->exec();

Save that as `command.php`.


Create A Command Object
-----------------------

Here is an example `Command` object to print 'Hello World!' to standard output:

    <?php
    namespace vendor\package;
    use aura\cli\Command;

    class Example extends Command
    {
        public action()
        {
            $this->stdio->outln('Hello World!');
        }
    }

Place the class file where your autoloader will find it.


Invoke The Command
------------------

Now that we have both the invoking script and the `Command` object, we can call the invoking script to execute the `Command`.

    $ php /path/to/command.php

The invoking script will load the `Command` command object and execute it; when it runs, it will "Hello World!".


Basic Usage
===========

Action and Input/Output
-----------------------

The logic for your `Command` goes in the `action()` method. In the example below, we perform some basic input/output.

    <?php
    namespace vendor\package;
    use aura\cli\Command;

    class Example extends Command
    {
        public function action()
        {
            $this->stdio->outln('Hello World!');
            $this->stdio->out('Please enter some text: ');
            $input = $this->stdio->in();
            $this->stdio->errln('You entered ' . $input);
        }
    }

When you invoke that `Command`, it will output "Hello World!", ask for some input, and then print that input to the error stream.

Use the `$stdio` object to work with standard input/output streams.  Its methods are:

- `outln()` and `out()`: Print to stdout, with or without a line ending.

- `errln()` and `err()`: Print to stderr, with or without a line ending.

- `inln()` and `in()`: Read from stdin until the user hits enter; `inln()` leaves the trailing line ending in place, whereas `in()` strips it.


Pre-Action and Post-Action
--------------------------

There are two behavioral hooks on the CLI `Command`; these are invoked through the `$signal` signal manager.  Use the pre- and post-action methods to perform logic before and after the action.

    <?php
    namespace vendor\package;
    use aura\cli\Command;

    class Example extends Command
    {
        protected $input = 'foo bar baz';
        
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
    }


Argument Params
---------------

We may wish to pass information as part of the invocation.  To read this information while in the `Command`, use `$this->params`.

    <?php
    namespace vendor\package;
    use aura\cli\Command;

    class Example extends Command
    {
        public function action()
        {
            foreach ($this->params as $key => $val) {
                $this->stdio->outln("Param $key is '$val'.");
            }
        }
    }
    
For example, if you issue ...
    
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

To work with options, we first define them in the `$options` array of the `Command`.  Then we can retrieve the option values through the `$getopt` object.

To define an option, do something like the following:

    <?php
    namespace vendor\package;
    use aura\cli\Command;
    use aura\cli\Option;
    
    class Example extends Command
    {
        protected $options = array(
            'foo_bar' => array(
                'long'    => 'foo-bar',
                'short'   => 'f',
                'param'   => Option::PARAM_REQUIRED,
                'multi'   => false,
                'default' => null,
            ),
        );
        
        public function action()
        {
            $this->stdio->out("The value of -f/--foo-bar is ")
            $this->stdio->outln($this->getopt->foo_bar);
        }
    }

When you invoke the above `Command` like this ...

    $ php command.php --foo-bar=gir

... it will print this output:

    The value of -f/--foo-bar is gir.

The `$options` array is keyed on what you want as the option name, and each element is an array of option definition keys:

- `'long'`: The long form of the option, which is passed by prefixing it with two dashes at the command line.  A long-form param value is passed by following it with an equals sign and the value; e.g., `--foo-bar=some_value`. Leave this empty if you do not want a long-form option.

- `'short'`: The short form of the option, which is passed by prefixing it with one dash at the command line.  A short-form param value is passed by following it with a space and the value; e.g., `-f some_value`. Leave this empty if you do not want a short-form option.

- `'param'`: Is a a param value required for the option, is it optional, or is it disallowed?  Use `Option::PARAM_REQUIRED` to force a param value to be passed, `Option::PARAM_OPTIONAL` to allow a value to be passed or not, or `Option::PARAM_REJECTED` to disallow any value from being passed.

- `'multi'`: Is the option allowed to be passed multiple times in the same command?  E.g., "-f foo -f bar -f zim" will make the option value an array with three entries (`'foo'`, `'bar'`, and `'zim'`).

- `'default'`: The default value for the option if it is not passed.

After you have defined the options and passed them at the command line, you can read them from the `$getopt` object as magic read-only properties.  Thus, for the above option named as `'foo_bar'`, you can retrieve its value by using `$this->getopt->foo_bar`.


Signals and Skipping Action
---------------------------

Before the `action()` method runs, the `Command` sends a `'pre_action'` signal to the signal manager, with the `Command` object itself as the only parameter.  You can add your own handlers to the signal manager to execute pre-action behaviors from the `Command` sender.  (The `Command` adds its own `preAction()` method to the signal manager at construction time.)

If you want to stop the `action()` from being run, a signal handler for `'pre_action'` can call the `skipAction()` method on the `Command`. This will skip the `action()` method and go directly to the `'post_action'` signal.

After the `action()` method runs or is skipped, the `Command` sends a `'post_action'` signal to the signal manager, with the `Command` object itself as the only parameter.  You can add your own handlers to the signal manager to execute post-action behaviors from the `Command` sender.  (The `Command` adds its own `postAction()` method to the signal manager at construction time.)


Command Factory
===============

(implemented; documentation is forthcoming)

