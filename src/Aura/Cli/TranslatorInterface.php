<?php
/**
 * 
 * This file is part of the Aura Project for PHP.
 * 
 * @package Aura.Cli
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Cli;

/**
 * 
 * TranslatorInterface
 * 
 * @package Aura.Cli
 * 
 */
interface TranslatorInterface
{
    /**
     * 
     * Translate the key with the token values replaced.
     * 
     * @param string $key The message key.
     * 
     * @param array $tokens_values The message placeholder tokens and their
     * replacement values.
     * 
     * @return string The translated string.
     * 
     */
    public function translate($key, array $tokens_values = []);
}
