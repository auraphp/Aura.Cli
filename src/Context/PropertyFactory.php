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
        return new Values($this->get('_SERVER'));
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
        return new Values($this->get('_ENV'));
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
