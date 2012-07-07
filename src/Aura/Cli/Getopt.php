<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @package Aura.Cli
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Cli;

/**
 * 
 * Retrieves and validates command-line options and parameter values.
 * 
 * @package Aura.Cli
 * 
 */
class Getopt
{
    /**
     * 
     * If an option is passed that is not defined, throw an exception.
     * 
     * @const bool
     * 
     */
    const STRICT = true;
    
    /**
     * 
     * Do not throw exceptions when undefined options are passed.
     * 
     * @const bool
     * 
     */
    const NON_STRICT = false;
    
    /**
     * 
     * A factory to create Option objects.
     * 
     * @var OptionFactory
     * 
     */
    protected $option_factory;
    
    /**
     * 
     * Definitions for recognized options.
     *      
     * @var array
     * 
     */
    protected $options = [];
    
    /**
     * 
     * Remaining non-option params after loading option values.
     * 
     * @var array
     * 
     */
    protected $params = [];
    
    /**
     * 
     * The incoming arguments, typically from $_SERVER['argv'].
     * 
     * @param array
     * 
     */
    protected $argv = [];
    
    /**
     * 
     * Constructor.
     * 
     * @param OptionFactory $option_factory A factory for Option objects.
     * 
     */
    public function __construct(OptionFactory $option_factory)
    {
        $this->option_factory = $option_factory;
    }
    
    /**
     * 
     * Make Option values available as magic readonly properties.
     * 
     * @param string $key The option name.
     * 
     * @return mixed The option value.
     * 
     */
    public function __get($key)
    {
        $option = $this->getOption($key);
        if ($option) {
            return $option->getValue();
        }
    }
    
    /**
     * 
     * Initializes the instance with option definitions.
     * 
     * @param array $opts An array of key-value pairs where the key is the
     * option name and the value is the option spec.
     * 
     * @param bool $strict Initialize in strict (true) or non-strict (false)
     * mode?
     * 
     * @return void
     * 
     */
    public function init(array $opts, $strict = self::STRICT)
    {
        if ($this->options) {
            throw new Exception('Already initialized.');
        }
        
        foreach ($opts as $name => $spec) {
            if (! is_array($spec)) {
                throw new \UnexpectedValueException;
            }
            $spec['name'] = $name;
            $this->options[$name] = $this->option_factory->newInstance($spec);
        }
        
        $this->strict = $strict;
    }
    
    /**
     * 
     * Returns all the Option definition objects.
     * 
     * @return array An array of Option objects.
     * 
     */
    public function getOptions()
    {
        return $this->options;
    }
    
    /**
     * 
     * Returns a single Option definition object by its property name.
     * 
     * @var string $key The property name of the option.
     * 
     * @return Option
     * 
     */
    public function getOption($prop)
    {
        if (array_key_exists($prop, $this->options)) {
            return $this->options[$prop];
        }
        
        if ($this->strict) {
            throw new Exception\OptionNotDefined($prop);
        }
    }
    
    /**
     * 
     * Returns an array of all Option names and their values.
     * 
     * @return array
     * 
     */
    public function getOptionValues()
    {
        $vals = [];
        foreach ($this->getOptions() as $name => $option) {
            $vals[$name] = $option->getValue();
        }
        return $vals;
    }
    
    /**
     * 
     * Returns the value of a single Option by name.
     * 
     * @param string $name The option name to get a value for.
     * 
     * @return mixed
     * 
     */
    public function getOptionValue($name)
    {
        $option = $this->getOption($name);
        if ($option) {
            return $option->getValue();
        }
    }
    
    /**
     * 
     * Returns an array of all numeric parameters.
     * 
     * @return array
     * 
     */
    public function getParams()
    {
        return $this->params;
    }
    
    /**
     * 
     * Returns a single Option definition object by its long-format name.
     * 
     * @var string $key The long-format name of the option.
     * 
     * @return Option
     * 
     */
    public function getLongOption($long)
    {
        foreach ($this->options as $option) {
            if ($option->getLong() == $long) {
                return $option;
            }
        }
        
        if ($this->strict) {
            throw new Exception\OptionNotDefined("--$long");
        }
    }
    
    /**
     * 
     * Returns a single Option definition object by its short-format name.
     * 
     * @var string $key The long-format name of the option.
     * 
     * @return Option
     * 
     */
    public function getShortOption($char)
    {
        foreach ($this->options as $option) {
            if ($option->getShort() == $char) {
                return $option;
            }
        }
        
        if ($this->strict) {
            throw new Exception\OptionNotDefined("-$char");
        }
    }
    
    /**
     * 
     * Loads Option values from an argument array, placing option values
     * in the defined Option objects and placing non-option params in a 
     * `$params` variable.
     * 
     * @param array $argv An argument array, typically from $_SERVER['argv'].
     * 
     * @return void
     * 
     */
    public function load(array $argv)
    {
        // hold onto the argv source
        $this->argv = $argv;
        
        // remaining non-option params
        $params = [];
        
        // flag to say when we've reached the end of options
        $done = false;
        
        // shift each element from the top of the $argv source
        while ($this->argv) {
            
            // get the next argument
            $arg = array_shift($this->argv);
            
            // after a plain double-dash, all values are numeric (not options)
            if ($arg == '--') {
                $done = true;
                continue;
            }
            
            // if we're reached the end of options, just add to the params
            if ($done) {
                $this->params[] = $arg;
                continue;
            }
            
            // long option, short option, or numeric param?
            if (substr($arg, 0, 2) == '--') {
                $this->loadLong($arg);
            } elseif (substr($arg, 0, 1) == '-') {
                $this->loadShort($arg);
            } else {
                $this->params[] = $arg;
            }
        }
    }
    
    /**
     * 
     * Parses a long-form option.
     * 
     * @param string $spec The `$argv` element, e.g. "--foo" or "--bar=baz".
     * 
     * @return void
     * 
     */
    protected function loadLong($spec)
    {
        // take the leading "--" off the specification
        $spec = substr($spec, 2);
        
        // split the spec into name and value
        $pos = strpos($spec, '=');
        if ($pos === false) {
            $name  = $spec;
            $value = null;
        } else {
            $name  = substr($spec, 0, $pos);
            $value = substr($spec, $pos + 1);
        }
        
        // get the option object
        $option = $this->getLongOption($name);
        if (! $option) {
            return;
        }
        
        // if param is required but not present, blow up
        if ($option->isParamRequired() && $value === null) {
            throw new Exception\OptionParamRequired;
        }
        
        // if params are rejected and one is present, blow up
        if ($option->isParamRejected() && $value !== null) {
            throw new Exception\OptionParamRejected;
        }
        
        // if param is optional but not present, set to true
        if ($option->isParamOptional() && $value === null) {
            $option->setValue(true);
        } else {
            $option->setValue($value);
        }
    }
    
    /**
     * 
     * Parses a short-form option (or cluster of options).
     * 
     * @param string $arg The `$argv` element, e.g. "-f" or "-fbz".
     * 
     * @return void
     * 
     */
    protected function loadShort($spec)
    {
        // if we have a string like "-abcd", process as a cluster
        if (strlen($spec) > 2) {
            return $this->loadShortCluster($spec);
        }
        
        // get the option character (after the first "-")
        $char = substr($spec, 1);
        
        // get the option object
        $option = $this->getShortOption($char);
        if (! $option) {
            return;
        }
        
        // if the option does not need a param, flag as true and move on
        if ($option->isParamRejected()) {
            $option->setValue(true);
            return;
        }
        
        // the option was defined as needing a param (required or optional).
        // peek at the next element from $argv ...
        $value = reset($this->argv);
        
        // ... and see if it's a param. can be empty, too, which indicates
        // then end of the arguments.
        $is_param = ! empty($value) && substr($value, 0, 1) != '-';
        
        if (! $is_param && $option->isParamOptional()) {
            // the next value is not a param, but a param is optional,
            // so flag the option as true and move on.
            $option->setValue(true);
            return;
        }
        
        if (! $is_param && $option->isParamRequired()) {
            // the next value is not a param, but a param is required,
            // so blow up.
            throw new Exception\OptionParamRequired;
        }
        
        // at this point, the value is a param, and it's optional or required.
        // pull it out of the arguments for real ...
        $value = array_shift($this->argv);
        
        // ... and set it.
        $option->setValue($value);
    }
    
    /**
     * 
     * Parses a cluster of short options.
     * 
     * @param string $spec The short-option cluster (e.g. "-abcd").
     * 
     * @return void
     * 
     */
    protected function loadShortCluster($spec)
    {
        // drop the leading dash
        $spec = substr($spec, 1);
        
        // loop through each character in the cluster
        $k = strlen($spec);
        for ($i = 0; $i < $k; $i ++) {
            
            // get the right character from the cluster
            $char = $spec[$i];
            
            // get the option object
            $option = $this->getShortOption($char);
            if (! $option) {
                continue;
            }
            
            // can't process params in a cluster
            if ($option->isParamRequired()) {
                throw new Exception\OptionParamRequired;
            }
            
            // otherwise, set the value as a flag
            $option->setValue(true);
        }
    }
}
