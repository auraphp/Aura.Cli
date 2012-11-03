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
 * Translator to translate the message
 * 
 * @package Aura.Cli
 * 
 */
class Translator implements TranslatorInterface
{
    /**
     *
     * @var array
     */
    protected $messages = [];

    public function __construct(
        $locale,
        array $catalog
    ) {
        $this->messages = $catalog['messages'][$locale];
    }

    /**
     * 
     * Translate the key with the token values replaced
     * 
     * @param string $key
     * 
     * @param array $tokens_values
     * 
     * @return string
     * 
     */
    public function translate($key, array $tokens_values = [])
    {
        // retain the message string
        $message = $this->messages[$key];

        // are there token replacement values?
        if (! $tokens_values) {
            // no, return the message string as-is
            return $message;
        }

        // do string replacements
        foreach ($tokens_values as $token => $value) {
            $message = str_replace("{:$token}", $value, $message);
        }
        
        // done!
        return $message;
    }
}
