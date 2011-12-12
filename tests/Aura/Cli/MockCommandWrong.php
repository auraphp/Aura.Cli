<?php
namespace Aura\Cli;
use Aura\Cli\Stdio as Stdio;
use Aura\Cli\Getopt as Getopt;
class MockCommandWrong extends MockCommand
{
    public function __construct(
        Stdio $stdio,
        Getopt $getopt
    ) {
        parent::__construct($stdio, $getopt);
        throw new \UnexpectedValueException('Child of RuntimeException');
    }
}
