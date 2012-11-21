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
 * An object-oriented wrapper for resource streams.
 * 
 * @package Aura.Cli
 * 
 */
class StdioResource
{
    /**
     *
     * file pointer
     * 
     * @var resource 
     * 
     */
    protected $handle;

    /**
     * 
     * filename
     * 
     * @var string 
     *
     */
    protected $filename;

    /**
     * 
     * The mode parameter specifies the type of access you require to the stream.
     *
     * @var string 
     * 
     */
    protected $mode;

    /**
     * 
     * Constructor
     * 
     * @param string $filename
     * 
     * @param string $mode
     * 
     */
    public function __construct($filename, $mode)
    {
        $this->filename = $filename;
        $this->mode     = $mode;
        $this->handle   = fopen($this->filename, $this->mode);
    }

    /**
     * 
     * Destructor
     * 
     */
    public function __destruct()
    {
        if ($this->handle) {
            fclose($this->handle);
        }
    }

    /**
     * 
     * Binary-safe file read
     * 
     * @return string
     * 
     */
    public function fread()
    {
        return fread($this->handle, 8192);
    }

    /**
     * 
     * Binary-safe file write
     * 
     * @param string $string
     * 
     * @return int
     * 
     */
    public function fwrite($string)
    {
        return fwrite($this->handle, $string);
    }

    /**
     * 
     * Rewind the position of a file pointer
     * 
     * @return type
     * 
     */
    public function rewind()
    {
        return rewind($this->handle);
    }

    /**
     * 
     * Gets line from file pointer
     * 
     * @return string
     * 
     */
    public function fgets()
    {
        return fgets($this->handle);
    }

    /**
     * 
     * Determine if a file descriptor is an interactive terminal
     * 
     * @return bool
     * 
     */
    public function isPosixTty()
    {
        return @posix_isatty($this->handle);
    }
}
