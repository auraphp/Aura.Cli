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
     * Translate a key into a string, interpolating token values as needed.
     * 
     * @param string $key
     * 
     * @param array $tokens_values
     * 
     * @return string
     * 
     */
    public function translate($key, array $tokens_values = []);
}
