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
    protected $message_only = true;
}
