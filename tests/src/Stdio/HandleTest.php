<?php
namespace Aura\Cli\Stdio;

class HandleTest extends \PHPUnit_Framework_TestCase
{
    protected $handle;
    
    protected function setUp()
    {
        parent::setUp();
        $this->handle = new Handle('php://memory', 'w+');
    }

    public function testGetName()
    {
        $expect = 'php://memory';
        $actual = $this->handle->getName();
        $this->assertSame($expect, $actual);
    }

    public function testGetMode()
    {
        $expect = 'w+';
        $actual = $this->handle->getMode();
        $this->assertSame($expect, $actual);
    }
    
    /* everything else is covered in the StdioTest */

}
