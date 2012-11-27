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
namespace Aura\Cli\Exception;

use Aura\Cli\Exception;

/**
 * 
 * Asked for an option that is not defined.
 * 
 * @package Aura.Cli
 * 
 */
class OptionNotDefined extends Exception
{
    /**
     * 
     * Should this exception print only the message text?
     * 
     * @var bool
     * 
     */
    protected $message_only = true;
}
