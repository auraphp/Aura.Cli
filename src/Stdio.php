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
namespace Aura\Cli;

use Aura\Cli\Stdio\Handle;
use Aura\Cli\Stdio\Vt100;

/**
 * 
 * Provides a wrapper for standard input/output streams.
 * 
 * @package Aura.Cli
 * 
 */
class Stdio
{
    /**
     * 
     * A Handle object for standard input.
     * 
     * @var Handle
     * 
     */
    protected $stdin;

    /**
     * 
     * A Handle object for standard output.
     * 
     * @var Handle
     * 
     */
    protected $stdout;

    /**
     * 
     * A Handle object for standard error.
     * 
     * @var Handle
     * 
     */
    protected $stderr;

    /**
     * 
     * A Vt100 object to format output.
     * 
     * @var Vt100
     * 
     */
    protected $vt100;

    /**
     * 
     * Constructor.
     * 
     * @param Handle $stdin A Handle object for standard input.
     * 
     * @param Handle $stdout A Handle object for standard output.
     * 
     * @param Handle $stderr A Handle object for standard error.
     * 
     * @param Vt100 $vt100 A VT100 formatting object.
     * 
     */
    public function __construct (
        Handle $stdin,
        Handle $stdout,
        Handle $stderr,
        Vt100 $vt100
    ) {
        $this->stdin  = $stdin;
        $this->stdout = $stdout;
        $this->stderr = $stderr;
        $this->vt100  = $vt100;
    }

    /**
     * 
     * Returns the standard input Handle object.
     * 
     * @return Handle
     * 
     */
    public function getStdin()
    {
        return $this->stdin;
    }

    /**
     * 
     * Returns the standard output Handle object.
     * 
     * @return Handle
     * 
     */
    public function getStdout()
    {
        return $this->stdout;
    }

    /**
     * 
     * Returns the standard error Handle object.
     * 
     * @return Handle
     * 
     */
    public function getStderr()
    {
        return $this->stderr;
    }

    /**
     * 
     * Gets user input from the command line and trims the end-of-line.
     * 
     * @return string
     * 
     */
    public function in()
    {
        return rtrim($this->stdin->fgets(), PHP_EOL);
    }

    /**
     * 
     * Gets user input from the command line and leaves the end-of-line in
     * place.
     * 
     * @return string
     * 
     */
    public function inln()
    {
        return $this->stdin->fgets();
    }

    /**
     * 
     * Prints text to standard output via the Vt100 formatter **without** 
     * a trailing newline.
     * 
     * @param string $string The text to print to standard output.
     * 
     * @return null
     * 
     */
    public function out($string = null)
    {
        $this->vt100->write($this->stdout, $string);
    }

    /**
     * 
     * Prints text to standard output via the Vt100 formatter **with** 
     * a trailing newline.
     * 
     * @param string $string The text to print to standard output.
     * 
     * @return null
     * 
     */
    public function outln($string = null)
    {
        $this->vt100->writeln($this->stdout, $string);
    }

    /**
     * 
     * Prints text to standard error via the Vt100 formatter **without** 
     * a trailing newline.
     * 
     * @param string $string The text to print to standard error.
     * 
     * @return null
     * 
     */
    public function err($string = null)
    {
        $this->vt100->write($this->stderr, $string);
    }

    /**
     * 
     * Prints text to standard error via the Vt100 formatter **without** 
     * a trailing newline.
     * 
     * @param string $string The text to print to standard error.
     * 
     * @return null
     * 
     */
    public function errln($string = null)
    {
        $this->vt100->writeln($this->stderr, $string);
    }
}
