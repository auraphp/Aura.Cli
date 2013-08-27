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
namespace Aura\Cli\Stdio;

/**
 * 
 * An object-oriented wrapper for file/stream resources.
 * 
 * @package Aura.Cli
 * 
 */
class Handle
{
    /**
     *
     * The resource represented by the handle.
     * 
     * @var resource 
     * 
     */
    protected $resource;

    /**
     * 
     * The resource name, typically a file or stream name.
     * 
     * @var string 
     *
     */
    protected $name;

    /**
     * 
     * The mode under which the resource was opened.
     *
     * @var string 
     * 
     */
    protected $mode;

    /**
     * 
     * Constructor.
     * 
     * @param string $name The resource to open, typically a PHP stream
     * like "php://stdout".
     * 
     * @param string $mode Open the resource in this mode; e.g., "w+".
     * 
     */
    public function __construct($name, $mode)
    {
        $this->name     = $name;
        $this->mode     = $mode;
        $this->resource = fopen($this->name, $this->mode);
    }

    /**
     * 
     * Destructor; closes the resource if it is not already closed.
     * 
     * @return null
     * 
     */
    public function __destruct()
    {
        if ($this->resource) {
            fclose($this->resource);
        }
    }

    /**
     * 
     * Returns the resource name.
     * 
     * @return string
     * 
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * 
     * Returns the resource mode.
     * 
     * @return string
     * 
     */
    public function getMode()
    {
        return $this->mode;
    }
    
    /**
     * 
     * Reads 8192 bytes from the resource.
     * 
     * @return mixed The string read on success, or boolean false on failure.
     * 
     */
    public function fread()
    {
        return fread($this->resource, 8192);
    }

    /**
     * 
     * Writes a string to the resource.
     * 
     * @param string $string
     * 
     * @return int The number of bytes written on success, or boolean false on
     * failure.
     * 
     */
    public function fwrite($string)
    {
        return fwrite($this->resource, $string);
    }

    /**
     * 
     * Rewinds the resource pointer.
     * 
     * @return bool
     * 
     */
    public function rewind()
    {
        return rewind($this->resource);
    }

    /**
     * 
     * Reads a line from the resource.
     * 
     * @return string The line on success, or boolean false on failure.
     * 
     */
    public function fgets()
    {
        return fgets($this->resource);
    }

    /**
     * 
     * Does the resource represent an interactive terminal?
     * 
     * @return bool
     * 
     */
    public function isPosix()
    {
        // silence posix_isatty() errors regarding non-standard resources,
        // e.g. php://memory
        $level = error_reporting(0);
        $value = posix_isatty($this->resource);
        error_reporting($level);
        return $value;
    }
}
