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
 * Creates property objects for the Context object.
 * 
 * @package Aura.Cli
 * 
 */
class PropertyFactory
{
    /**
     * 
     * A copy of $GLOBALS.
     * 
     * @param array
     * 
     */
    protected $globals;
    
    /**
     * 
     * Constructor.
     * 
     * @param array $globals A copy of $GLOBALS.
     * 
     */
    public function __construct(array $globals)
    {
        $this->globals = $globals;
    }
    
    /**
     * 
     * Returns a Values object representing `$_SERVER`.
     * 
     * @return Values
     * 
     */
    public function newServer()
    {
        return new Values($this->getGlobals('_SERVER'));
    }
    
    /**
     * 
     * Returns a Values object representing `$_ENV`.
     * 
     * @return Values
     * 
     */
    public function newEnv()
    {
        return new Values($this->getGlobals('_ENV'));
    }
    
    /**
     * 
     * Returns a Values object representing the command line options.
     * 
     * @param array $data Data for the Values object.
     * 
     * @return Values
     * 
     */
    public function newOpts(array $data = [])
    {
        return new Values($data);
    }
    
    /**
     * 
     * A Values object representing the command line arguments.
     * 
     * @param array $data Data for the Values object.
     * 
     * @return Values
     * 
     */
    public function newArgs(array $data = [])
    {
        return new Values($data);
    }
    
    /**
     * 
     * Given an array of option definitions, returns an array where the first
     * element is a Values object representing the command line options, and
     * the second elemend is a Values object representing the command line
     * arguments.
     * 
     * @param array $defs An array of Getopt option definitions.
     * 
     * @return array
     * 
     */
    public function newOptsArgs(array $defs)
    {
        $getopt = new Getopt;
        $getopt->setDefs($defs);
        $getopt->setArgv($this->getGlobals('argv'));
        return [
            $this->newOpts($getopt->getOpts()),
            $this->newArgs($getopt->getArgs()),
        ];
    }
    
    /**
     * 
     * Returns a $GLOBALS array element.
     * 
     * @param string $key The $GLOBALS array key.
     * 
     * @return array
     * 
     */
    protected function getGlobals($key)
    {
        return isset($this->globals[$key])
             ? $this->globals[$key]
             : [];
    }
}
