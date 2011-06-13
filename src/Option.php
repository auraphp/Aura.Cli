<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Cli;

/**
 * 
 * Defines a single command-line option.
 * 
 * @package Aura.Cli
 * 
 */
class Option
{
    /**
     * 
     * A parameter value is required to be present for this option.
     * 
     * @const string
     * 
     */
    const PARAM_REQUIRED = 'required';
    
    /**
     * 
     * Any parameter value on this option will be rejected.
     * 
     * @const string
     * 
     */
    const PARAM_REJECTED = 'rejected';
    
    /**
     * 
     * A parameter value may or may not be present on this option.
     * 
     * @const string
     * 
     */
    const PARAM_OPTIONAL = 'optional';
    
    /**
     * 
     * The property name of the option.
     * 
     * @var string
     * 
     */
    protected $name = null;
    
    /**
     * 
     * The long name of the option.
     * 
     * @var string
     * 
     */
    protected $long = null;
    
    /**
     * 
     * The short name of the option.
     * 
     * @var string
     * 
     */
    protected $short = null;
    
    /**
     * 
     * When the option is present, will a be parameter required, optional, or
     * rejected (i.e., no param allowed) ?
     * 
     * @var string
     * 
     */
    protected $param = self::PARAM_OPTIONAL;
    
    /**
     * 
     * Can the option be specified multiple times?
     * 
     * @var bool
     * 
     */
    protected $multi = null;
    
    /**
     * 
     * The default value for the option param.
     * 
     * @var string
     * 
     */
    protected $default = null;
    
    /**
     * 
     * The option value as set from the command line.
     * 
     * @var string
     * 
     */
    protected $value = null;
    
    /**
     * 
     * Initialized the object with an option definition array.
     * 
     * @param array $data The option definition.
     * 
     * @return void
     * 
     */
    public function __construct(
        $name    = null,
        $long    = null,
        $short   = null,
        $param   = null,
        $multi   = null,
        $default = null
    ) {
        // load into properties
        $this->name    = (string) $name;
        $this->long    = (string) $long;
        $this->short   = (string) $short;
        $this->param   = $param ?: static::PARAM_OPTIONAL;
        $this->multi   = (bool) $multi;
        $this->default = $default;
        
        // always need a name
        if (! $this->name) {
            throw new Exception\OptionName;
        }
        
        // always need a long format or a short format.
        if (! $this->long && ! $this->short) {
            // auto-add a long format
            $this->long = str_replace('_', '-', $this->name);
        }
        
        // param has to be boolean or null
        $ok = $this->param === static::PARAM_REQUIRED
           || $this->param === static::PARAM_REJECTED
           || $this->param === static::PARAM_OPTIONAL;
           
        if (! $ok) {
            throw new Exception\OptionParam;
        }
        
        // preset the value to an array if multiple values are allowed
        if ($this->multi) {
            $this->value = array();
        } else {
            $this->value = null;
        }
    }
    
    /**
     * 
     * Sets the option value.
     * 
     * @param mixed $value The value to set.
     * 
     * @return void
     * 
     */
    public function setValue($value)
    {
        if ($this->isParamRequired() && trim($value) === '') {
            throw new Exception\OptionParamRequired;
        }
        
        if ($this->isMulti()) {
            $this->value[] = $value;
            return;
        }
        
        if ($this->value !== null) {
            throw new Exception\OptionNotMulti;
        }
        
        $this->value = $value;
    }
    
    /**
     * 
     * Gets the option value.
     * 
     * @return mixed The value as set from the command line, or the default
     * value if not set from the command line.
     * 
     */
    public function getValue()
    {
        if ($this->value === null) {
            return $this->default;
        } else {
            return $this->value;
        }
    }
    
    /**
     * 
     * Gets the long name for this option.
     * 
     * @return string
     * 
     */
    public function getLong()
    {
        return $this->long;
    }
    
    /**
     * 
     * Gets the short name for this option.
     * 
     * @return string
     * 
     */
    public function getShort()
    {
        return $this->short;
    }
    
    /**
     * 
     * Gets the property name for this option.
     * 
     * @return string
     * 
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * 
     * Gets the default value of this option.
     * 
     * @return mixed
     * 
     */
    public function getDefault()
    {
        return $this->default;
    }
    
    /**
     * 
     * Can this option be present multiple times?
     * 
     * @return bool
     * 
     */
    public function isMulti()
    {
        return $this->multi;
    }
    
    /**
     * 
     * Is a param value required to be present on this option?
     * 
     * @return bool
     * 
     */
    public function isParamRequired()
    {
        return $this->param === static::PARAM_REQUIRED;
    }
    
    /**
     * 
     * Is a param value required *not* to be present on this option?
     * 
     * @return bool
     * 
     */
    public function isParamRejected()
    {
        return $this->param === static::PARAM_REJECTED;
    }
    
    /**
     * 
     * Is a param value optional on this option?
     * 
     * @return bool
     * 
     */
    public function isParamOptional()
    {
        return $this->param === static::PARAM_OPTIONAL;
    }
}
