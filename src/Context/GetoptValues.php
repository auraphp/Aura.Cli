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
 * A read-only representation of values values.
 * 
 * @package Aura.Cli
 * 
 */
class GetoptValues extends GlobalValues
{
    protected $errors;
    
    /**
     * 
     * Constructor.
     * 
     * @param array $values The values values to be represented by this array.
     * 
     */
    public function __construct(array $values = [], array $errors = [])
    {
        parent::__construct($values);
        $this->errors = $errors;
    }
    
    public function hasErrors()
    {
        return (bool) $this->errors;
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
}
