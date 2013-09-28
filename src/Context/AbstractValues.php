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

/**
 * 
 * A read-only representation of data values.
 * 
 * @package Aura.Cli
 * 
 */
class AbstractValues
{
    /**
     * 
     * The values represented by this object.
     * 
     * @var array
     * 
     */
    protected $values;
    
    /**
     * 
     * Constructor.
     * 
     * @param array $values The values to be represented by this object.
     * 
     */
    public function __construct(array $values = array())
    {
        $this->values = $values;
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
}
