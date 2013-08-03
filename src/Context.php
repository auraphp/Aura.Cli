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

use Aura\Cli\Context\PropertyFactory;

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
     * Values taken from $_SERVER['argv'].
     * 
     * @var array
     * 
     */
    protected $argv;

    /**
     * 
     * Imported $_ENV values.
     * 
     * @var array
     * 
     */
    protected $env;

    protected $opts;
    
    protected $args;
    
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
     * A factory to create property objects.
     * 
     * @var PropertyFactory
     * 
     */
    protected $property_factory;
    
    /**
     * 
     * Constructor.
     * 
     */
    public function __construct(PropertyFactory $property_factory)
    {
        $this->property_factory = $property_factory;
        $this->args             = $this->property_factory->newArgs();
        $this->env              = $this->property_factory->newEnv();
        $this->opts             = $this->property_factory->newOpts();
        $this->server           = $this->property_factory->newServer();
    }

    public function __get($key)
    {
        return $this->$key;
    }
    
    public function getopt(array $defs)
    {
        $instances = $this->property_factory->newOptsArgs($defs);
        list($this->opts, $this->args) = $instances;
    }
}
