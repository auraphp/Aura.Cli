<?php
namespace aura\cli;
use aura\cli\Stdio as Stdio;
use aura\cli\Getopt as Getopt;
class MockControllerWrong extends MockController
{
    public function __construct(
        Stdio $stdio,
        Getopt $getopt
    ) {
        parent::__construct($stdio, $getopt);
        throw new \UnexpectedValueException('Child of RuntimeException');
    }
}
