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

use Aura\Cli\Stdio\Resource;
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
     * A Resource object for standard input.
     * 
     * @var Resource
     * 
     */
    protected $stdin;

    /**
     * 
     * A Resource object for standard output.
     * 
     * @var Resource
     * 
     */
    protected $stdout;

    /**
     * 
     * A Resource object for standard error.
     * 
     * @var Resource
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
     * @param Resource $stdin A Resource object for standard input.
     * 
     * @param Resource $stdout A Resource object for standard output.
     * 
     * @param Resource $stderr A Resource object for standard error.
     * 
     * @param Vt100 $vt100 A VT100 formatting object.
     * 
     */
    public function __construct (
        Resource $stdin,
        Resource $stdout,
        Resource $stderr,
        Vt100 $vt100
    ) {
        $this->stdin  = $stdin;
        $this->stdout = $stdout;
        $this->stderr = $stderr;
        $this->vt100  = $vt100;
    }

    /**
     * 
     * Returns the standard input Resource object.
     * 
     * @return Resource
     * 
     */
    public function getStdin()
    {
        return $this->stdin;
    }

    /**
     * 
     * Returns the standard output Resource object.
     * 
     * @return Resource
     * 
     */
    public function getStdout()
    {
        return $this->stdout;
    }

    /**
     * 
     * Returns the standard error Resource object.
     * 
     * @return Resource
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
     * @return void
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
     * @return void
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
     * @return void
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
     * @return void
     * 
     */
    public function errln($string = null)
    {
        $this->vt100->writeln($this->stderr, $string);
    }
}
