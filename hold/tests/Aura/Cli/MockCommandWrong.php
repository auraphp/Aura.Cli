<?php
namespace Aura\Cli;

use Aura\Cli\MockException;

class MockCommandWrong extends MockCommand
{
    protected function action()
    {
        throw new MockException;
    }
}
