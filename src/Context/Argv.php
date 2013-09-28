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
 * A read-only representation of $argv values.
 * 
 * @package Aura.Cli
 * 
 */
class Argv extends AbstractValues
{
    /**
     * 
     * Shifts the first element off the data values and returns it.
     * 
     * @return mixed
     * 
     */
    public function shift()
    {
        return array_shift($this->values);
    }
}
