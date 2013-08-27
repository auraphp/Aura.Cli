<?php
namespace Aura\Cli\Stdio;

class FormatterTest extends \PHPUnit_Framework_TestCase
{
    protected $formatter;

    protected function setUp()
    {
        parent::setUp();
        $this->formatter = new Formatter;
    }

    public function testFormat()
    {
        $text = '<<bold>>bold%percent<<reset>>';
        $esc    = chr(27);
        $expect = "{$esc}[1mbold%percent{$esc}[0m";
        $actual = $this->formatter->format($text, true);
        $this->assertSame($expect, $actual);
        
        $text = '<<bold>>bold%percent<<reset>>';
        $expect = "bold%percent";
        $actual = $this->formatter->format($text, false);
        $this->assertSame($expect, $actual);
    }
    
    // public function testWrite_win()
    // {
    //     $this->formatter->setPhpOs('win');
    //     
    //     $text = '<<bold>>bold%percent<<reset>>';
    //     $expect = "bold%percent";
    //     
    //     $handle = new Handle('php://memory', 'w+');
    //     $this->formatter->write($handle, $text);
    //     $handle->rewind();
    //     $actual = $handle->fread(8192);
    //     unset($handle);
    //     
    //     $this->assertSame($expect, $actual);
    // }
    // 
    // public function testWriteln_win()
    // {
    //     $this->formatter->setPhpOs('win');
    //     
    //     $text = '<<bold>>bold%percent<<reset>>';
    //     $expect = "bold%percent" . PHP_EOL;
    //     
    //     $handle = new Handle('php://memory', 'w+');
    //     $this->formatter->writeln($handle, $text);
    //     $handle->rewind();
    //     $actual = $handle->fread(8192);
    //     unset($handle);
    //     
    //     $this->assertSame($expect, $actual);
    // }
    
}
