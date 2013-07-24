<?php
namespace Aura\Cli;

use UnexpectedValueException;

class MockCommandWrongAgain extends MockCommand
{
    protected function action()
    {
        throw new UnexpectedValueException;
    }
}
