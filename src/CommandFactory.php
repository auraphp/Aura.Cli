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
 * A factory to create Command objects.
 * 
 * @package aura.cli
 * 
 */
class CommandFactory
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
     * Command classes.
     * 
     * @var array
     * 
     */
    protected $map = array();
    
    /**
     * 
     * A Command class to use when no class exists for a mapped name.
     * 
     * @var string
     * 
     */
    protected $not_found = null;
    
    /**
     * 
     * A Command class to use when no class exists for a mapped name.
     * 
     * @param ForgeInterface $forge A Forge to create objects.
     * 
     * @param array $map A map of command names to Command classes.
     * 
     * @param string $not_found A Command class to use when no class
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
     * Creates and returns a Command class based on a command name.
     * 
     * @param string $name A command name that maps to a Command class;
     * if this name is not found in the map, use the `$not_found` class.
     * 
     * @return Command
     * 
     * @throws Exception_CommandFactory when no mapped class can be found
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
            throw new Exception_CommandFactory("No class found for '$name' and no 'not-found' Command specified.");
        }
        
        return $this->forge->newInstance($class);
    }
    
    public function map($name, $class)
    {
        $this->map[$name] = $class;
    }
}
