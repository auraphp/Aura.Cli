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
 * A factory to create exception objects with translated messages.
 *
 * @package Aura.Cli
 *
 */
class ExceptionFactory
{
    /**
     *
     * A translator object.
     *
     * @var TranslatorInterface
     *
     */
    protected $translator;
    
    /**
     *
     * Constructor.
     *
     * @param TranslatorInterface $translator A translator object.
     *
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    
    /**
     *
     * Returns a new Exception instance.
     *
     * @param string $key The type of exception to return; e.g.,
     * 'ERR_OPTION_NOT_DEFINED'.
     *
     * @param array $tokens_values Translation placeholder tokens and their
     * replacement values.
     *
     */
    public function newInstance($key, $tokens_values = [])
    {
        // get a message translation
        $message = $this->translator->translate($key, $tokens_values);
        
        // strip 'ERR_' prefix.
        // ERR_OPTION_NOT_DEFINED -> OPTION_NOT_DEFINED
        $class = substr($key, 4);
        
        // underscores to spaces, lowercase all, uppercase words.
        // OPTION_NOT_DEFINED -> OptionNotDefined
        $class = ucwords(strtolower(str_replace('_', ' ', $class)));
        
        // remove spaces and add prefix
        $class = 'Aura\Cli\Exception\\' . str_replace(' ', '', $class);
        
        // return the new exception, with a return code of 1
        return new $class($message, 1);
    }
}
