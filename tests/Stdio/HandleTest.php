<?php
namespace Aura\Cli\Stdio;

class HandleTest extends \PHPUnit\Framework\TestCase
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

    public function testForceTty()
    {
        $handle = new Handle('php://memory', 'w+', 'win', null, true);
        $this->assertFalse($handle->isPosix());
        $this->assertTrue($handle->isTty());
    }

    public function testMemoryIsNotTty()
    {
        $handle = new Handle('php://memory', 'w+');
        $this->assertFalse($handle->isTty());
    }
}
