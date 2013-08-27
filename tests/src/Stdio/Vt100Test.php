<?php
namespace Aura\Cli\Stdio;

class Vt100Test extends \PHPUnit_Framework_TestCase
{
    protected $vt100;

    protected function setUp()
    {
        parent::setUp();
        $this->vt100 = new Vt100;
    }

    public function testFormat()
    {
        $text = '<<bold>>bold%percent<<reset>>';
        $esc    = chr(27);
        $expect = "{$esc}[1mbold%percent{$esc}[0m";
        $actual = $this->vt100->format($text);
        $this->assertSame($expect, $actual);
    }

    public function testStrip()
    {
        $text = '<<bold>>bold%percent<<reset>>';
        $expect = "bold%percent";
        $actual = $this->vt100->strip($text);
        $this->assertSame($expect, $actual);
    }
    
    public function testSetAndGetPosix()
    {
        $list = [true, false, null];
        foreach ($list as $flag) {
            $this->vt100->setPosix($flag);
            $this->assertSame($flag, $this->vt100->getPosix());
        }
    }
    
    public function testSetAndGetPhpOs()
    {
        $actual = $this->vt100->getPhpOs();
        $this->assertSame(PHP_OS, $actual);
        
        $this->vt100->setPhpOs('win');
        $actual = $this->vt100->getPhpOs();
        $this->assertSame('win', $actual);
    }
    
    public function testWrite()
    {
        $text = '<<bold>>bold%percent<<reset>>';
        $expect = "bold%percent";
        
        $handle = new Handle('php://memory', 'w+');
        $this->vt100->write($handle, $text);
        $handle->rewind();
        $actual = $handle->fread(8192);
        unset($handle);
        
        $this->assertSame($expect, $actual);
    }
    
    public function testWriteln()
    {
        $text = '<<bold>>bold%percent<<reset>>';
        $expect = "bold%percent" . PHP_EOL;
        
        $handle = new Handle('php://memory', 'w+');
        $this->vt100->writeln($handle, $text);
        $handle->rewind();
        $actual = $handle->fread(8192);
        unset($handle);
        
        $this->assertSame($expect, $actual);
    }
    
    public function testWrite_posix()
    {
        $this->vt100->setPosix(true);
        
        $text = '<<bold>>bold%percent<<reset>>';
        $esc    = chr(27);
        $expect = "{$esc}[1mbold%percent{$esc}[0m";
        
        $handle = new Handle('php://memory', 'w+');
        $this->vt100->write($handle, $text);
        $handle->rewind();
        $actual = $handle->fread(8192);
        unset($handle);
        
        $this->assertSame($expect, $actual);
    }
    
    public function testWriteln_posix()
    {
        $this->vt100->setPosix(true);
        
        $text = '<<bold>>bold%percent<<reset>>';
        $esc    = chr(27);
        $expect = "{$esc}[1mbold%percent{$esc}[0m" . PHP_EOL;
        
        $handle = new Handle('php://memory', 'w+');
        $this->vt100->writeln($handle, $text);
        $handle->rewind();
        $actual = $handle->fread(8192);
        unset($handle);
        
        $this->assertSame($expect, $actual);
    }
    
    public function testWrite_win()
    {
        $this->vt100->setPhpOs('win');
        
        $text = '<<bold>>bold%percent<<reset>>';
        $expect = "bold%percent";
        
        $handle = new Handle('php://memory', 'w+');
        $this->vt100->write($handle, $text);
        $handle->rewind();
        $actual = $handle->fread(8192);
        unset($handle);
        
        $this->assertSame($expect, $actual);
    }
    
    public function testWriteln_win()
    {
        $this->vt100->setPhpOs('win');
        
        $text = '<<bold>>bold%percent<<reset>>';
        $expect = "bold%percent" . PHP_EOL;
        
        $handle = new Handle('php://memory', 'w+');
        $this->vt100->writeln($handle, $text);
        $handle->rewind();
        $actual = $handle->fread(8192);
        unset($handle);
        
        $this->assertSame($expect, $actual);
    }
    
}
