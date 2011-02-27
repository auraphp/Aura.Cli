<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace aura\cli;
use aura\di\ForgeInterface as ForgeInterface;

/**
 * 
 * Dispatches a command to a controller. This is a ControllerFactory by another 
 * name.
 * 
 * @package aura.cli
 * 
 * @todo Throw an exception when a mapped controller cannot be instantiated.
 * 
 */
class ControllerFactory
{
    protected $forge;
    
    protected $controllers = array();
    
    protected $not_found = null;
    
    public function __construct(ForgeInterface $forge, array $controllers = null, $not_found = null)
    {
        $this->forge       = $forge;
        $this->controllers = (array) $controllers;
        $this->not_found   = $not_found;
    }
    
    public function newInstance($name)
    {
        if (isset($this->controllers[$name])) {
            $class = $this->controllers[$name];
        } elseif ($this->not_found) {
            $class = $this->not_found;
        } else {
            throw new Exception("No class found for '$name' and no 'not-found' controller specified.");
        }
        
        return $this->forge->newInstance($class);
    }
}
