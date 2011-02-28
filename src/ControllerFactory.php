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
 * A factory to create Controller objects.
 * 
 * @package aura.cli
 * 
 */
class ControllerFactory
{
    /**
     * 
     * A Forge to create objects.
     * 
     * @var aura\di\ForgeInterface
     * 
     */
    protected $forge;
    
    /**
     * 
     * A map of names (called at the command line) to their corresponding
     * Controller classes.
     * 
     * @var array
     * 
     */
    protected $map = array();
    
    /**
     * 
     * A Controller class to use when no class exists for a mapped name.
     * 
     * @var string
     * 
     */
    protected $not_found = null;
    
    /**
     * 
     * A Controller class to use when no class exists for a mapped name.
     * 
     * @param ForgeInterface $forge A Forge to create objects.
     * 
     * @param array $map A map of command names to controller classes.
     * 
     * @param string $not_found A Controller class to use when no class
     * can be found for a mapped name.
     * 
     */
    public function __construct(
        ForgeInterface $forge,
        array $map = null,
        $not_found = null
    ) {
        $this->forge     = $forge;
        $this->map       = (array) $map;
        $this->not_found = $not_found;
    }
    
    /**
     * 
     * Creates and returns a Controller class based on a command name.
     * 
     * @param string $name A command name that maps to a Controller class;
     * if this name is not found in the map, use the `$not_found` class.
     * 
     * @return Controller
     * 
     * @throws Exception_ControllerFactory when no mapped class can be found
     * and no `$not_found` class is specified.
     * 
     */
    public function newInstance($name)
    {
        if (isset($this->map[$name])) {
            $class = $this->map[$name];
        } elseif ($this->not_found) {
            $class = $this->not_found;
        } else {
            throw new Exception_ControllerFactory("No class found for '$name' and no 'not-found' controller specified.");
        }
        
        return $this->forge->newInstance($class);
    }
}
