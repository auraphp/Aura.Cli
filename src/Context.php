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
     * Positional arguments.
     * 
     * @var Context\Values
     * 
     */
    protected $args;
    
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
     * Values from short flags and long options.
     * 
     * @var Context\Values
     * 
     */
    protected $opts;
    
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
     * @param PropertyFactory $property_factory A factory to create propery
     * objects.
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
    
    /**
     * 
     * Set the options definitions, then reload the `$opts` and `$args`
     * property objects.
     * 
     * @param array $defs The option definitions.
     * 
     * @return null
     * 
     * @see Context\Getopt::setDefs()
     * 
     */
    public function getopt(array $defs)
    {
        $instances = $this->property_factory->newOptsArgs($defs);
        list($this->opts, $this->args) = $instances;
    }
}
