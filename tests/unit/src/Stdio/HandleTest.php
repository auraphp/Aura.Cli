<?php
namespace Aura\Cli\Stdio;

class HandleTest extends \PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $handle = new Handle('php://memory', 'w+');

        $expect = 'php://memory';
        $actual = $handle->getName();
        $this->assertSame($expect, $actual);

        $expect = 'w+';
        $actual = $handle->getMode();
        $this->assertSame($expect, $actual);
    }

    public function testWinPosix()
    {
        $handle = new Handle('php://memory', 'w+', 'win');
        $this->assertFalse($handle->isPosix());
    }

    public function testForcePosix()
    {
        $handle = new Handle('php://memory', 'w+', null, false);
        $this->assertFalse($handle->isPosix());
    }
}
