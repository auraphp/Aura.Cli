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

use Aura\Cli\Context\Argv;
use Aura\Cli\Context\Env;
use Aura\Cli\Context\Getopt;
use Aura\Cli\Context\Server;

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
     * Imported $argv values.
     * 
     * @var Argv
     * 
     */
    protected $argv;

    /**
     * 
     * Imported $_ENV values.
     * 
     * @var Env
     * 
     */
    protected $env;

    /**
     * 
     * A prototype Getopt object.
     * 
     * @var Getopt
     * 
     */
    protected $getopt;
    
    /**
     * 
     * Imported $_SERVER values.
     * 
     * @var Server
     * 
     */
    protected $server;

    /**
     * 
     * Constructor.
     * 
     */
    public function __construct(
        Env $env,
        Server $server,
        Argv $argv,
        Getopt $getopt
    ) {
        $this->env    = $env;
        $this->server = $server;
        $this->argv   = $argv;
        $this->getopt = $getopt;
    }

    /**
     * 
     * Magic read for property objects.
     * 
     * @param string $key The property to get.
     * 
     * @return mixed A property object.
     * 
     */
    public function __get($key)
    {
        if (in_array($key, array('env', 'server', 'argv'))) {
            return $this->$key;
        }
    }
    
    /**
     * 
     * Returns a new Getopt instance.
     * 
     * @param array $options Option definitions for the Getopt instance.
     * 
     * @return Getopt
     * 
     */
    public function getopt(array $options)
    {
        $getopt = clone $this->getopt;
        $getopt->setOptions($options);
        $getopt->parse($this->argv->get());
        return $getopt;
    }
}
