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
 * The option does not allow multiple values.
 *
 * @package Aura.Cli
 *
 */
class OptionNotMulti extends Exception
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
