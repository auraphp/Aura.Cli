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
     * @param PropertyFactory $property_factory A factory to create propery
     * objects.
     * 
     */
    public function __construct(PropertyFactory $property_factory)
    {
        $this->env    = $property_factory->newEnv();
        $this->server = $property_factory->newServer();
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
}
