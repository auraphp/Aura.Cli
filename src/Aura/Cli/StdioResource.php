<?php
namespace Aura\Cli;

/**
 * 
 * An object-oriented wrapper for resource streams.
 * 
 */
class StdioResource
{
    protected $handle;
    
    protected $filename;
    
    protected $mode;
    
    public function __construct($filename, $mode)
    {
        $this->filename = $filename;
        $this->mode     = $mode;
        $this->handle   = fopen($this->filename, $this->mode);
    }
    
    public function __destruct()
    {
        fclose($this->handle);
    }
    
    public function fread()
    {
        return fread($this->handle, 8192);
    }
    
    public function fwrite($string)
    {
        return fwrite($this->handle, $string);
    }
    
    public function rewind()
    {
        return rewind($this->handle);
    }
    
    public function fgets()
    {
        return fgets($this->handle);
    }
    
    public function isPosixTty()
    {
        return @posix_isatty($this->handle);
    }
}
