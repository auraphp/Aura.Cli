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

/**
 * 
 * A factory for creating Context and Stdio objects.
 * 
 * @package Aura.Cli
 * 
 */
class CliFactory
{
    /**
     * 
     * Returns a new Context object.
     * 
     * @param array $globals A copy of $GLOBALS.
     * 
     * @return Context
     * 
     */
    public function newContext(array $globals)
    {
        return new Context(
            new Context\ValuesFactory(
                $globals,
                new Context\Getopt
            )
        );
    }
    
    /**
     * 
     * Returns a new Stdio object.
     * 
     * @param string $stdin The resource to open for stdin.
     * 
     * @param string $stdout The resource to open for stdout.
     * 
     * @param string $stderr The resource to open for stderr.
     * 
     * @return Stdio
     * 
     */
    public function newStdio(
        $stdin = 'php://stdin',
        $stdout = 'php://stdout',
        $stderr = 'php://stderr'
    ) {
        return new Stdio(
            new Stdio\Handle($stdin, 'r'),
            new Stdio\Handle($stdout, 'w+'),
            new Stdio\Handle($stderr, 'w+'),
            new Stdio\Formatter
        );
    }
}
