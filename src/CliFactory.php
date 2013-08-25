<?php
namespace Aura\Cli;

class CliFactory
{
    public function newContext(array $globals)
    {
        return new Context(
            new Context\ValuesFactory(
                new Context\Getopt,
                $GLOBALS
            )
        );
    }
    
    public function newStdio(
        $stdin = 'php://stdin',
        $stdout = 'php://stdout',
        $stderr = 'php://stderr'
    ) {
        return new Stdio(
            new Stdio\Handle($stdin, 'r'),
            new Stdio\Handle($stdout, 'w+'),
            new Stdio\Handle($stderr, 'w+'),
            new Stdio\Vt100
        );
    }
}
