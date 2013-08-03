<?php
namespace Aura\Cli\Stdio;

class HandleTest extends \PHPUnit_Framework_TestCase
{
    protected $handle;
    
    protected function setUp()
    {
        parent::setUp();
        $this->resource  = new Handle('php://memory', 'w+');
    }

    public function testGetFilename()
    {
        $expect = 'php://memory';
        $actual = $this->resource->getFilename();
        $this->assertSame($expect, $actual);
    }

    public function testGetMode()
    {
        $expect = 'w+';
        $actual = $this->resource->getMode();
        $this->assertSame($expect, $actual);
    }
    
    /* everything else is covered in the StdioTest */

}
