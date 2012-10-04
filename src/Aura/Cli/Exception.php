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

/**
 * 
 * Generic package exception.
 * 
 * @package Aura.Cli
 * 
 */
class Exception extends \Exception
{
    protected $localized_message_key = 'LOCALIZED_MESSAGE_KEY';

    public function getLocalizedMessageKey()
    {
        return $this->localized_message_key;
    }
}
