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

use Aura\Cli\Context\ValuesFactory;

/**
 * 
 * Collection point for information about the command line execution context.
 * 
 * @package Aura.Cli
 * 
 */
class Context
{
    /**
     * 
     * Imported $_ENV values.
     * 
     * @var Context\Values
     * 
     */
    protected $env;

    /**
     * 
     * Imported $_SERVER values.
     * 
     * @var array
     * 
     */
    protected $server;

    /**
     * 
     * Constructor.
     * 
     * @param ValuesFactory $property_factory A factory to create propery
     * objects.
     * 
     */
    public function __construct(ValuesFactory $values_factory)
    {
        $this->values_factory = $values_factory;
        
        $this->argv   = $this->values_factory->newArgv();
        $this->env    = $this->values_factory->newEnv();
        $this->server = $this->values_factory->newServer();
    }

    /**
     * 
     * Magic read for property objects.
     * 
     * @param string $key The property to get.
     * 
     * @return Context\Value A property object.
     * 
     */
    public function __get($key)
    {
        return $this->$key;
    }
    
    public function getopt(array $opt_defs = [], array $arg_defs = [])
    {
        return $this->values_factory->newGetopt($opt_defs, $arg_defs);
    }
}
