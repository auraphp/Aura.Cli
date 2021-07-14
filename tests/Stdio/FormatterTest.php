<?php
namespace Aura\Cli\Stdio;

class FormatterTest extends \PHPUnit\Framework\TestCase
{
    protected $formatter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->formatter = new Formatter;
    }

    public function testFormat()
    {
        // escape character
        $esc = chr(27);

        // posix
        $text = '<<bold>>bold%percent<<reset>>';
        $expect = "{$esc}[1mbold%percent{$esc}[0m";
        $actual = $this->formatter->format($text, true);
        $this->assertSame($expect, $actual);

        // non-posix
        $text = '<<bold>>bold%percent<<reset>>';
        $expect = "bold%percent";
        $actual = $this->formatter->format($text, false);
        $this->assertSame($expect, $actual);
    }
}
