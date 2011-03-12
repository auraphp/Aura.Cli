Introduction
============

The Aura CLI package provides a system for creating and executing CLI `Command` objects.  It includes facilities for parsing command-line options and handling standard input/output; it is [signal](https://github.com/auraphp/aura.signal) aware as well.


Basic Usage
===========

Action and Input/Output
-----------------------

The logic for the `Command` goes in the `action()` method. In the example below, we perform some basic input/output.

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
            $this->stdio->errln('Input was ' . $input);
        }
    }

When we invoke that `Command`, it will output "Hello World!", ask for some input, and then print that input to the error stream.

Use the `$stdio` object to work with standard input/output streams.  Its methods are:

- `outln()` and `out()`: Print to stdout, with or without a line ending.

- `errln()` and `err()`: Print to stderr, with or without a line ending.

- `inln()` and `in()`: Read from stdin until the user hits enter; `inln()` leaves the trailing line ending in place, whereas `in()` strips it.


Signal Hooks
------------

There are four signal hooks on the CLI `Command`; these are invoked through the `$signal` signal manager.  Use the pre- and post-action methods to perform logic before and after the action; use pre- and post-exec methods to perform setup and teardown.

    <?php
    namespace vendor\package;
    use aura\cli\Command;

    class Example extends Command
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

When we invoke the above `Command` like this ...

    $ php command.php --foo-bar=gir

... it will print this output:

    The value of -f/--foo-bar is gir.

The `$options` array is keyed on what we want as the option name, and each element is an array of option definition keys:

- `'long'`: The long form of the option, which is passed by prefixing it with two dashes at the command line.  A long-form param value is passed by following it with an equals sign and the value; e.g., `--foo-bar=some_value`. Leave this empty if we do not want a long-form option.

- `'short'`: The short form of the option, which is passed by prefixing it with one dash at the command line.  A short-form param value is passed by following it with a space and the value; e.g., `-f some_value`. Leave this empty if we do not want a short-form option.

- `'param'`: Is a a param value required for the option, is it optional, or is it disallowed?  Use `Option::PARAM_REQUIRED` to force a param value to be passed, `Option::PARAM_OPTIONAL` to allow a value to be passed or not, or `Option::PARAM_REJECTED` to disallow any value from being passed.

- `'multi'`: Is the option allowed to be passed multiple times in the same command?  E.g., "-f foo -f bar -f zim" will make the option value an array with three entries (`'foo'`, `'bar'`, and `'zim'`).

- `'default'`: The default value for the option if it is not passed.

After we have defined the options and passed them at the command line, we can read them from the `$getopt` object as magic read-only properties.  Thus, for the above option named as `'foo_bar'`, we can retrieve its value by using `$this->getopt->foo_bar`.


Signals and Skipping Action
---------------------------

At `exec()` time, the `Command` sends a `'pre_exec'` signal to the signal manager, with the `Command` object itself as the only parameter. Use this to set up the `Command` object as needed.

Before the `action()` method runs, the `Command` sends a `'pre_action'` signal to the signal manager, with the `Command` object itself as the only parameter.  

To stop the `action()` from being run, a signal handler for `'pre_action'` can call the `skipAction()` method on the `Command`. This will skip the `action()` method and go directly to the `'post_exec'` signal.

After the `action()` method runs, the `Command` sends a `'post_action'` signal to the signal manager, with the `Command` object itself as the only parameter.  (If the `action()` was skipped, the `'post_action'` signal will not be sent.)

Finally, at the end of `exec()`, the `Command` sends a `'pre_exec'` signal to the signal manager, with the `Command` object itself as the only parameter. Use this to clean up after the `Command` object as needed.


Invoking Script and Command Factory
===================================

Before we can run a `Command` object, we need what we will call an "invoking script."  It is the equivalent of a web boostrap script, but in a CLI environment. The invoking script is what we run from the command line; it sets up the execution environment, then creates the `Command` object and executes it.

In the invoking script, do not instantiate the `Command` directly. Instead, create an array that maps short command names to their corresponding class names, and use a `CommandFactory` to create the `Command` object based on the short name.

For example, the following code will instantiate a `vendor\package\Example` class from the `vendor.package/src` direcotry and execute it.  This is a little long; it makes use of various other Aura packages for necessary functionality.

    <?php
    // create a map of command names to command classes
    $command_map = array(
        'example' => 'vendor\package\Example',
    );
    
    // set up an autoloader
    $loader = require '/path/to/aura.autoload/scripts/instance.php';
    $loader->register();
    $loader->addPrefix('aura\di\\',        '/path/to/aura.di/src');
    $loader->addPrefix('aura\signal\\',    '/path/to/aura.signal/src');
    $loader->addPrefix('aura\cli\\',       '/path/to/aura.cli/src');
    $loader->addPrefix('vendor\package\\', '/path/to/vendor.package/src');
    
    // instantiate and configure the DI container.
    use aura\di\Container;
    use aura\di\Forge;
    use aura\di\Config;
    $di = new Container(new Forge(new Config));
    require '/path/to/aura.signal/config/default.php';
    require '/path/to/aura.cli/config/default.php';
    
    // get the cli context object from the DI container, then discard the
    // invoking script name from the cli context argument values
    $context = $di->get('cli_context');
    $context->shiftArgv();
    
    // get the command factory from the DI container and add the command map
    $factory = $di->get('cli_command_factory');
    foreach ($command_map as $name => $class) {
        $factory->map($name, $class);
    }
    
    // using first cli context argument as the short command name, 
    // get a new command object instance and then execute it.
    try {
        $command = $factory->newInstance($context->shiftArgv());
        $command->exec();
    } catch (Exception $e) {
        echo $e->getMessage() . PHP_EOL;
    }

Save the script as `command.php`.  After that, we can issue `php /path/to/command.php example` and it will run the `vendor\package\Example` class.

Wow that looks like a lot of code.  If you use the aura.system framework package, it does all that for you, but if you want to avoid the system as a whole, that's what you have to do.
