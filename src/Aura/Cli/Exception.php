<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @package Aura.Cli
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Cli;

use Exception as PhpException;

/**
 * 
 * Generic package exception.
 * 
 * @package Aura.Cli
 * 
 */
class Exception extends PhpException
{
    /**
     * 
     * Should this exception print only the message text?
     * 
     * @var bool
     * 
     */
    protected $message_only = false;
    
    /**
     * 
     * Should this exception print only the message text?
     * 
     * @return bool
     * 
     */
    public function getMessageOnly()
    {
        return (bool) $this->message_only;
    }
}
