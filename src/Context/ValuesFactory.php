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
class ValuesFactory
{
    /**
     * 
     * A copy of $GLOBALS.
     * 
     * @var array
     * 
     */
    protected $globals;
    
    /**
     * 
     * A getopt parser.
     * 
     * @var Getopt
     * 
     */
    protected $getopt;
    
    /**
     * 
     * Constructor.
     * 
     * @param array $globals A copy of $GLOBALS.
     * 
     * @param Getopt $getopt A getopt parser.
     * 
     */
    public function __construct(
        array $globals,
        Getopt $getopt
    ) {
        $this->globals = $globals;
        $this->getopt = $getopt;
        $argv = $this->get('argv');
        $this->getopt->setInput($argv);
    }
    
    /**
     * 
     * Returns a values object representing `$_SERVER`.
     * 
     * @return GlobalValues
     * 
     */
    public function newServer()
    {
        return new GlobalValues($this->get('_SERVER'));
    }
    
    /**
     * 
     * Returns a Values object representing `$_ENV`.
     * 
     * @return GlobalValues
     * 
     */
    public function newEnv()
    {
        return new GlobalValues($this->get('_ENV'));
    }
    
    /**
     * 
     * Returns a values object representing `$argv`.
     * 
     * @return GlobalValues
     * 
     */
    public function newArgv()
    {
        return new GlobalValues($this->get('argv'));
    }
    
    /**
     * 
     * Returns a values object representing options and arguments.
     * 
     * @return GetoptValues
     * 
     */
    public function newGetopt(array $opt_defs, array $arg_defs)
    {
        $this->getopt->setOptDefs($opt_defs);
        $this->getopt->setArgDefs($arg_defs);
        $this->getopt->parse($opt_defs, $arg_defs);
        return new GetoptValues(
            $this->getopt->getValues(),
            $this->getopt->getErrors()
        );
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
    protected function get($key)
    {
        return isset($this->globals[$key])
             ? $this->globals[$key]
             : [];
    }
}
