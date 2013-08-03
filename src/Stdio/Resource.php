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
 * An object-oriented wrapper for resource streams.
 * 
 * @package Aura.Cli
 * 
 */
class Resource
{
    /**
     *
     * Resource handle.
     * 
     * @var resource 
     * 
     */
    protected $handle;

    /**
     * 
     * The file name represented by the resource handle.
     * 
     * @var string 
     *
     */
    protected $filename;

    /**
     * 
     * The mode under which the resource handle was opened.
     *
     * @var string 
     * 
     */
    protected $mode;

    /**
     * 
     * Constructor.
     * 
     * @param string $filename The file name to open, typically a PHP stream
     * like `php://stdout`.
     * 
     * @param string $mode The mode under which to open the stream.
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
     * Destructor; closes the handle if it is not already closed.
     * 
     * @return null
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
     * Returns the file name of the resource handle.
     * 
     * @return string
     * 
     */
    public function getFilename()
    {
        return $this->filename;
    }
    
    /**
     * 
     * Returns the mode under which the resource handle was opened.
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
     * Reads 8192 bytes from the resource handle.
     * 
     * @return mixed The string read on success, or boolean false on failure.
     * 
     */
    public function fread()
    {
        return fread($this->handle, 8192);
    }

    /**
     * 
     * Writes a string the resource handle.
     * 
     * @param string $string
     * 
     * @return int The number of bytes written on success, or boolean false on
     * failure.
     * 
     */
    public function fwrite($string)
    {
        return fwrite($this->handle, $string);
    }

    /**
     * 
     * Rewinds the resource handle.
     * 
     * @return bool
     * 
     */
    public function rewind()
    {
        return rewind($this->handle);
    }

    /**
     * 
     * Reads a line from the resource handle.
     * 
     * @return string The line on success, or boolean false on failure.
     * 
     */
    public function fgets()
    {
        return fgets($this->handle);
    }

    /**
     * 
     * Is the resource handle an interactive terminal?
     * 
     * @return bool
     * 
     */
    public function isPosixTty()
    {
        // silence posix_isatty() errors regarding non-standard handles,
        // e.g. php://memory
        $level = error_reporting(0);
        $value = posix_isatty($this->handle);
        error_reporting($level);
        return $value;
    }
}
