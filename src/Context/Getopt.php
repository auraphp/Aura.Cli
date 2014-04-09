<?php
/**
 * 
 * This file is part of Aura for PHP.
 * 
 * @package Aura.Cli
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Cli\Context;

use Aura\Cli\Exception;
use Aura\Cli\GetoptParser;

/**
 * 
 * Parses and retains command line option and argument values.
 * 
 * @package Aura.Cli
 * 
 */
class Getopt extends AbstractValues
{
    /**
     * 
     * An array of error messages related to getopt parsing.
     * 
     * @var array
     * 
     */
    protected $errors = array();
    
    public function __construct(
        array $values = array(),
        array $errors = array()
    ) {
        parent::__construct($values);
        $this->errors = $errors;
    }

   /**
     * 
     * Are there error messages related to getopt parsing?
     * 
     * @return bool
     * 
     */
    public function hasErrors()
    {
        return $this->errors ? true : false;
    }
    
    /**
     * 
     * Returns the error messages related to getopt parsing.
     * 
     * @return array
     * 
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
