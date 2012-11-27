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
 * The option requires that no parameter be present.
 * 
 * @package Aura.Cli
 * 
 */
class OptionParamRejected extends Exception
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
