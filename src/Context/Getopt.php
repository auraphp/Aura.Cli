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
namespace Aura\Cli\Context;

use Aura\Cli\Exception;
use Aura\Cli\GetoptParser;

/**
 * 
 * Parses and retains command line option and argument values.
 * 
 * @package Aura.Cli
 * 
 */
class Getopt extends AbstractValues
{
    /**
     * 
     * Option definitions.
     *      
     * @var array
     * 
     */
    protected $options = array();

    /**
     * 
     * An array of error messages generated while parsing.
     * 
     * @var array
     * 
     */
    protected $errors = array();
    
    /**
     * 
     * The values generated from parsing.
     * 
     * @var array
     * 
     */
    protected $values = array();
    
    protected $getopt_parser;

    public function __construct(GetoptParser $getopt_parser)
    {
        $this->getopt_parser = $getopt_parser;
    }

    /**
     * 
     * Sets the option definitions (both long options and short flags).
     * 
     * @param array $options Each element is a short flag character or a
     * long option name followed by 0, 1, or 2 colons: two colons means an optional
     * param, one colon means a required param, no colon means no param is
     * allowed. (Cf. <http://php.net/getopt>.)
     * 
     * @return null
     * 
     */
    public function setOptions($options)
    {
        $this->options = array();
        foreach ($options as $key => $val) {
            $this->setOption($key, $val);
        }
    }
    
    /**
     * 
     * Sets one option.
     * 
     * @param string $string The option definition string.
     * 
     * @param string $descr The option description (e.g. for help text).
     * 
     * @return null
     * 
     */
    public function setOption($string, $descr = null)
    {
        $option = $this->getopt_parser->getDefined($string, $descr);
        $this->options[$option['name']] = $option;
    }
    
    /**
     * 
     * Returns the option definitions.
     * 
     * @return array
     * 
     */
    public function getOptions()
    {
        return $this->options;
    }
    
    /**
     * 
     * Gets a single option definition converted to an array.
     * 
     * Looking for an undefined option will cause an error message, but will
     * otherwise proceed.
     * 
     * - Looking for an undefined short flag (e.g., 'u') returns
     *   `['name' => '-u', 'param' => 'rejected']`
     * 
     * - Looking for an undefined long option (e.g., 'undef') returns
     *   `['name' => '--undef', 'param' => 'optional']`
     * 
     * @param string $name The definition key to look for.
     * 
     * @return array An option definition array with two keys, 'name' (the
     * option name) and 'param' (whether a param is rejected, required, or
     * optional).
     * 
     */
    public function getOption($name)
    {
        // is the option defined?
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }
        
        // is the option aliased?
        foreach ($this->options as $option) {
            if ($option['alias'] == $name) {
                return $option;
            }
        }
        
        $this->errors[] = new Exception\OptionNotDefined(
            "The option '$name' is not recognized."
        );
        
        return $this->getopt_parser->getUndefined($name);
    }
    
    /**
     * 
     * Parses the input array according to the option and argument defintions.
     * 
     * @return bool True if parsing succeeded without errors, false if there
     * were errors.
     * 
     */
    public function parse(array $input)
    {
        // reset errors and values
        $this->errors = array();
        $this->values = array();
        
        // flag to say when we've reached the end of options
        $done = false;

        // sequential argument count;
        $args = 0;
        
        // loop through a copy of the input values to be parsed
        while ($input) {

            // shift each element from the top of the $input source
            $arg = array_shift($input);

            // after a plain double-dash, all values are args (not options)
            if ($arg == '--') {
                $done = true;
                continue;
            }

            // long option, short option, or numeric argument?
            if (! $done && substr($arg, 0, 2) == '--') {
                $this->setLongOptionValue($arg);
            } elseif (! $done && substr($arg, 0, 1) == '-') {
                $this->setShortFlagValue($input, $arg);
            } else {
                $this->values[$args ++] = $arg;
            }
        }
        
        // did parsing work without errors?
        return $this->errors ? false : true;
    }

    /**
     * 
     * Are there error messages?
     * 
     * @return bool
     * 
     */
    public function hasErrors()
    {
        return $this->errors ? true : false;
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
     * @param string $name The `$input` element, e.g. "--foo" or "--bar=baz".
     * 
     * @return null
     * 
     */
    protected function setLongOptionValue($name)
    {
        // split the spec into name and value
        $pos = strpos($name, '=');
        if ($pos === false) {
            $value = null;
        } else {
            $value = substr($name, $pos + 1);
            $name = substr($name, 0, $pos);
        }

        // get the option definition
        $option = $this->getOption($name);

        // if param is required but not present, error
        if ($option['param'] == 'required' && trim($value) === '') {
            $this->errors[] = new Exception\OptionParamRequired(
                "The option '$name' requires a parameter."
            );
            return;
        }

        // if params are rejected and one is present, error
        if ($option['param'] == 'rejected' && trim($value) !== '') {
            $this->errors[] = new Exception\OptionParamRejected(
                "The option '$name' does not accept a parameter."
            );
            return;
        }

        // if param is not present, set to true
        if (trim($value) === '') {
            $value = true;
        }
        
        // retain the value, and done
        $this->setValue($option, $value);
    }

    /**
     * 
     * Parses a short-form option (or cluster of options).
     * 
     * @param array $input The input array.
     * 
     * @param string $name The `$input` element, e.g. "-f" or "-fbz".
     * 
     * @return null
     * 
     */
    protected function setShortFlagValue(&$input, $name)
    {
        // if we have a string like "-abcd", process as a cluster
        if (strlen($name) > 2) {
            return $this->setShortFlagValues($name);
        }

        // get the option
        $option = $this->getOption($name);

        // if the option does not need a param, set as true and move on
        if ($option['param'] == 'rejected') {
            $this->setValue($option, true);
            return;
        }

        // the option was defined as needing a param (required or optional).
        // peek at the next element from the input ...
        $value = reset($input);

        // ... and see if it's a param. can be empty, too, which indicates
        // the end of the input.
        $is_param = ! empty($value) && substr($value, 0, 1) != '-';

        if (! $is_param && $option['param'] == 'optional') {
            // the next value is not a param, but a param is optional,
            // so flag the option as true and move on.
            $this->setValue($option, true);
            return;
        }

        if (! $is_param && $option['param'] == 'required') {
            // the next value is not a param, but a param is required
            $this->errors[] = new Exception\OptionParamRequired(
                "The option '$name' requires a parameter."
            );
            return;
        }

        // at this point, the value is a param, and it's optional or required.
        // pull it out of the input for real ...
        $value = array_shift($input);

        // ... and set it.
        $this->setValue($option, $value);
    }

    /**
     * 
     * Parses a cluster of short options.
     * 
     * @param string $chars The short-option cluster (e.g. "-abcd").
     * 
     * @return null
     * 
     */
    protected function setShortFlagValues($chars)
    {
        // drop the leading dash in the cluster
        $chars = substr($chars, 1);

        // go through each character in the cluster
        $chars = str_split($chars);
        while ($char = array_shift($chars)) {
            
            // get the option definition
            $option = $this->getOption("-$char");

            // can't process params in a cluster
            if ($option['param'] == 'required') {
                $this->errors[] = new Exception\OptionParamRequired(
                    "The option '-$char' requires a parameter."
                );
                continue;
            }

            // otherwise, set the value as true
            $this->setValue($option, true);
        }
    }
    
    /**
     * 
     * Sets an option value, adding to a value array for 'multi' values.
     * 
     * @param array $option The option array.
     * 
     * @param mixed $value The option value.
     * 
     * @return null
     * 
     */
    protected function setValue($option, $value)
    {
        if ($option['multi']) {
            $this->addMultiValue($option['name'], $value, $option['alias']);
        } else {
            $this->setSingleValue($option['name'], $value, $option['alias']);
        }
    }

    protected function addMultiValue($name, $value, $alias = null)
    {
        $this->values[$name][] = $value;
        if ($alias) {
            $this->values[$alias][] = $value;
        }
    }

    protected function setSingleValue($name, $value, $alias = null)
    {
        $this->values[$name] = $value;
        if ($alias) {
            $this->values[$alias] = $value;
        }
    }
}
