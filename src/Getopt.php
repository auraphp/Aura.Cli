<?php
/**
 * 
 * This file is part of Aura for PHP.
 * 
 * @package Aura.Cli
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Cli;

use Aura\Cli\Exception;
use UnexpectedValueException;

/**
 * 
 * Parses command line option and argument values.
 * 
 * @package Aura.Cli
 * 
 */
class Getopt
{
    /**
     * 
     * Option definitions (both long option and short flag forms).
     *      
     * @var array
     * 
     */
    protected $opt_defs = [];

    /**
     * 
     * Argument definitions (sequential postion to argument name).
     * 
     * @var array
     * 
     */
    protected $arg_defs = [];
    
    /**
     * 
     * The incoming arguments, typically from $_SERVER['argv'].
     * 
     * @var array
     * 
     */
    protected $argv = [];

    /**
     * 
     * An array of error messages generated while parsing.
     * 
     * @var array
     * 
     */
    protected $errors = [];
    
    /**
     * 
     * The values generated from parsing: options and flags are keyed on their
     * dash-prefixed names, sequential arguments are keyed on their integer
     * position, and named arguments are keyed on their names.
     * 
     * @var array
     * 
     */
    protected $values = [];
    
    /**
     * 
     * Set the option definitions (both long options and short flags).
     * 
     * @param array $opt_defs Each element is a short flag character or a
     * long option name followed by 0-2 colons: two colons means an optional
     * param, one colon means a required param, no colon means no param is
     * allowed. (Cf. <http://php.net/getopt>.)
     * 
     * @return null
     * 
     */
    public function setOptDefs($opt_defs)
    {
        $this->opt_defs = [];
        foreach ($opt_defs as $key => $val) {
            
            $def = [
                'name'  => null,
                'param' => null,
            ];
            
            if (is_int($key)) {
                // 0 => 'f:', 1 => 'bar::', etc
                $key = $val;
                $def['name'] = rtrim($val, ':');
            } else {
                // 'f:' => 'foo', etc
                $def['name'] = $val;
            }
            
            // is a param optional/required/rejected?
            if (substr($key, -2) == '::') {
                $def['param'] = 'optional';
            } elseif (substr($key, -1) == ':') {
                $def['param'] = 'required';
            } else {
                $def['param'] = 'rejected';
            }
            
            // retain the definition
            $key = rtrim($key, ':');
            $this->opt_defs[$key] = $def;
        }
    }
    
    /**
     * 
     * Returns the option definitions.
     * 
     * @return array
     * 
     */
    public function getOptDefs()
    {
        return $this->opt_defs;
    }
    
    /**
     * 
     * Gets a single option definition converted to an array.
     * 
     * Looking for an undefined option will cause an error message, but will
     * otherwise proceed.
     * 
     * - Looking for an undefined short flag (e.g., 'u') returns
     *   `['name' => 'u', 'param' => 'rejected']`
     * 
     * - Looking for an undefined long option (e.g., 'undef') returns
     *   `['name' => 'undef', 'param' => 'optional']`
     * 
     * @param string $key The definition key to look for.
     * 
     * @return array An option definition array with two keys, 'name' (the
     * option name) and 'param' (whether a param is rejected, required, or
     * optional).
     * 
     */
    public function getOptDef($key)
    {
        // is the option defined?
        if (isset($this->opt_defs[$key])) {
            return $this->opt_defs[$key];
        }
        
        // undefined; retain a message about it then deal with it
        if (strlen($key) == 1) {
            $opt = "-$key";
        } else {
            $opt = "--$key";
        }
        $this->errors[] = "The option '$opt' is not recognized.";
        
        // undefined short flags take no param
        if (strlen($key) == 1) {
            return ['name' => $key, 'param' => 'rejected'];
        }
        
        // undefined long options take an optional param
        return ['name' => $key, 'param' => 'optional'];
    }
    
    /**
     * 
     * Sets the names for sequential arguments.
     * 
     * @param array $arg_defs An array where element 0 is the name for
     * argument 0, element 1 for argument 1, etc.
     * 
     * @return null
     * 
     */
    public function setArgDefs(array $arg_defs)
    {
        $this->arg_defs = $arg_defs;
    }
    
    /**
     * 
     * Returns the names for sequential arguments.
     * 
     * @return array
     * 
     */
    public function getArgDefs()
    {
        return $this->arg_defs;
    }
    
    /**
     * 
     * Parses an argument array (e.g., $_SERVER['argv']) according to the
     * option and argument defintions.
     * 
     * @param array $argv An argument array, typically from `$_SERVER['argv']`.
     * 
     * @return bool True if parsing succeeded without errors, false if there
     * were errors.
     * 
     */
    public function parse(array $argv)
    {
        // hold onto the argv source
        $this->argv = $argv;

        // reset errors and values
        $this->errors = [];
        $this->values = [];
        
        // retain args locally
        $args = [];
        
        // flag to say when we've reached the end of options
        $done = false;

        // loop through the argument values to be parsed
        while ($this->argv) {

            // shift each element from the top of the $argv source
            $arg = array_shift($this->argv);

            // after a plain double-dash, all values are params (not options)
            if ($arg == '--') {
                $done = true;
                continue;
            }

            // if we're reached the end of options, just add to the arguments
            if ($done) {
                $args[] = $arg;
                continue;
            }

            // long option, short option, or numeric argument?
            if (substr($arg, 0, 2) == '--') {
                $this->setLongOptionValue($arg);
            } elseif (substr($arg, 0, 1) == '-') {
                $this->setShortFlagValue($arg);
            } else {
                $args[] = $arg;
            }
        }
        
        // retain the arguments as values, setting names as we go
        foreach ($args as $key => $val) {
            // retain the sequential version
            $this->values[$key] = $val;
            // also retain the named version
            if (isset($this->arg_defs[$key])) {
                $name = $this->arg_defs[$key];
                $this->values[$name] = $val;
            }
        }
        
        // did parsing work without errors?
        return $this->errors ? false : true;
    }

    /**
     * 
     * Returns a value.
     * 
     * @param string $key The key, if any, to get the value of; if null, will
     * return all values.
     * 
     * @param string $alt The alternative default value to return if the
     * requested key does not exist.
     * 
     * @return mixed The requested value, or the alternative default
     * value.
     * 
     */
    public function get($key = null, $alt = null)
    {
        if ($key === null) {
            return $this->values;
        }
        
        if (array_key_exists($key, $this->values)) {
            return $this->values[$key];
        }
        
        return $alt;
    }
    
    /**
     * 
     * Returns the error messages.
     * 
     * @return array
     * 
     */
    public function getErrors()
    {
        return $this->errors;
    }
    
    /**
     * 
     * Parses a long option.
     * 
     * @param string $key The `$argv` element, e.g. "--foo" or "--bar=baz".
     * 
     * @return null
     * 
     */
    protected function setLongOptionValue($key)
    {
        // take the leading "--" off the specification
        $key = substr($key, 2);
        
        // split the spec into name and value
        $pos = strpos($key, '=');
        if ($pos === false) {
            $val = null;
        } else {
            $val = substr($key, $pos + 1);
            $key = substr($key, 0, $pos);
        }

        // get the option definition
        $def = $this->getOptDef($key);

        // if param is required but not present, blow up
        if ($def['param'] == 'required' && trim($val) === '') {
            $this->errors[] = "The option '--$key' requires a parameter.";
            return;
        }

        // if params are rejected and one is present, blow up
        if ($def['param'] == 'rejected' && trim($val) !== '') {
            $this->errors[] = "The option '--$key' does not accept a parameter.";
            return;
        }

        // if param is not present, set to integer 1
        if (trim($val) === '') {
            $val = 1;
        }
        
        // retain the value, and done
        $this->setOptValue($def['name'], $val);
    }

    /**
     * 
     * Parses a short-form option (or cluster of options).
     * 
     * @param string $spec The `$argv` element, e.g. "-f" or "-fbz".
     * 
     * @return null
     * 
     */
    protected function setShortFlagValue($spec)
    {
        // if we have a string like "-abcd", process as a cluster
        if (strlen($spec) > 2) {
            return $this->setShortFlagValues($spec);
        }

        // get the option character (after the first "-")
        $char = substr($spec, 1);

        // get the option object
        $def = $this->getOptDef($char);

        // if the option does not need a param, set to integer 1 and move on
        if ($def['param'] == 'rejected') {
            $this->setOptValue($def['name'], 1);
            return;
        }

        // the option was defined as needing a param (required or optional).
        // peek at the next element from $argv ...
        $value = reset($this->argv);

        // ... and see if it's a param. can be empty, too, which indicates
        // then end of the arguments.
        $is_param = ! empty($value) && substr($value, 0, 1) != '-';

        if (! $is_param && $def['param'] == 'optional') {
            // the next value is not a param, but a param is optional,
            // so flag the option as integer 1 and move on.
            $this->setOptValue($def['name'], 1);
            return;
        }

        if (! $is_param && $def['param'] == 'required') {
            // the next value is not a param, but a param is required
            $this->errors[] = "The option '-$char' requires a parameter.";
            return;
        }

        // at this point, the value is a param, and it's optional or required.
        // pull it out of the arguments for real ...
        $value = array_shift($this->argv);

        // ... and set it.
        $this->setOptValue($def['name'], $value);
    }

    /**
     * 
     * Parses a cluster of short options.
     * 
     * @param string $spec The short-option cluster (e.g. "-abcd").
     * 
     * @return null
     * 
     */
    protected function setShortFlagValues($spec)
    {
        // drop the leading dash
        $spec = substr($spec, 1);

        // loop through each character in the cluster
        $k = strlen($spec);
        for ($i = 0; $i < $k; $i ++) {

            // get the right character from the cluster
            $char = $spec[$i];

            // get the option definition
            $def = $this->getOptDef($char);

            // can't process params in a cluster
            if ($def['param'] == 'required') {
                $this->errors[] = "The option '-$char' requires a parameter.";
                continue;
            }

            // otherwise, set the value as integer 1
            $this->setOptValue($def['name'], 1);
        }
    }
    
    /**
     * 
     * Sets an option value; if an option value is set multiple times, it is
     * automatically converted to an array.
     * 
     * @param array $name The option name.
     * 
     * @param mixed $value The option value.
     * 
     * @return null
     * 
     */
    protected function setOptValue($name, $value)
    {
        if (strlen($name) == 1) {
            $name = "-$name";
        } else {
            $name = "--$name";
        }
        
        if (! isset($this->values[$name])) {
            $this->values[$name] = $value;
            return;
        }
        
        if (is_int($value) && is_int($this->values[$name])) {
            $this->values[$name] += $value;
            return;
        }
        
        // force to an array
        settype($this->values[$name], 'array');
        $this->values[$name][] = $value;
    }
}
