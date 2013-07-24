<?php
namespace Aura\Cli\Context;

class Values
{
    protected $data;
    
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }
    
    /**
     * 
     * Get a single value and return it.
     * 
     * @param string $key The array key, if any, to get the 
     * value of.
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
            return $this->data;
        }
        
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
        
        return $alt;
    }
}
