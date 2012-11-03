<?php
namespace Aura\Cli;

class ExceptionFactory
{
    protected $translator;
    
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    
    public function newInstance($key, $tokens_values = [])
    {
        // get a message translation
        $message = $this->translator->translate($key, $tokens_values);
        
        // strip 'ERR_' prefix.
        // ERR_OPTION_NOT_DEFINED -> OPTINO_NOT_DEFINED
        $class = substr($key, 4);
        
        // underscores to spaces, lowercase all, uppercase words.
        // OPTION_NOT_DEFINED -> OptionNotDefined
        $class = ucwords(strtolower(str_replace('_', ' ', $class)));
        
        // remove spaces and add prefix
        $class = 'Aura\Cli\Exception\\' . str_replace(' ', '', $class);
        
        // return the new exception
        return new $class($message);
    }
}
