<?php
namespace aura\cli;
class Stdio {
    
    protected $stdin;
    
    protected $stdout;
    
    protected $stderr;
    
    protected $vt100;
    
    public function __construct (
        $stdin,
        $stdout,
        $stderr,
        \aura\cli\Vt100  $vt100
    ) {
        $this->stdin  = $stdin;
        $this->stdout = $stdout;
        $this->stderr = $stderr;
        $this->vt100  = $vt100;
    }
    
    public function getStdin()
    {
        return $this->stdin;
    }
    
    public function getStdout()
    {
        return $this->stdout;
    }
    
    public function getStderr()
    {
        return $this->stderr;
    }
    
    /**
     * 
     * Prints text to $stdout via $vt100 **without** a trailing newline.
     * 
     * @param string $string The text to print to STDOUT.
     * 
     * @return void
     * 
     */
    public function out($string = null)
    {
        $this->vt100->write($this->stdout, $string);
    }
    
    /**
     * 
     * Prints text to $stdout via $vt100 **with** a trailing newline.
     * 
     * @param string $string The text to print to STDOUT.
     * 
     * @return void
     * 
     */
    public function outln($string = null)
    {
        $this->vt100->writeln($this->stdout, $string);
    }
    
    /**
     * 
     * Prints text to $stderr via $vt100 **without** a trailing newline.
     * 
     * @param string $string The text to print to STDERR.
     * 
     * @return void
     * 
     */
    public function err($string = null)
    {
        $this->vt100->write($this->stderr, $string);
    }
    
    /**
     * 
     * Prints text to $stderr via $vt100 **with** a trailing newline.
     * 
     * Automatically replaces style-format codes for VT100 shell output.
     * 
     * @param string $string The text to print to STDERR.
     * 
     * @return void
     * 
     */
    public function errln($string = null)
    {
        $this->vt100->writeln($this->stderr, $string);
    }
}
