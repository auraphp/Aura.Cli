<?php
namespace Aura\Cli;

use Aura\Cli\Stdio\Handle;
use Aura\Cli\Stdio\Formatter;

class StdioTest extends \PHPUnit_Framework_TestCase
{
    protected $stdio;

    protected $stdin;

    protected $stdout;

    protected $stderr;

    protected $formatter;

    protected function setUp()
    {
        parent::setUp();
        $this->stdin  = new Handle('php://memory', 'r+');
        $this->stdout = new Handle('php://memory', 'w+');
        $this->stderr = new Handle('php://memory', 'w+');
        $this->formatter  = new Formatter;
        $this->stdio = new Stdio(
            $this->stdin,
            $this->stdout,
            $this->stderr,
            $this->formatter
        );
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testGetStdin()
    {
        $actual = $this->stdio->getStdin();
        $this->assertSame($this->stdin, $actual);
    }

    public function testGetStdout()
    {
        $actual = $this->stdio->getStdout();
        $this->assertSame($this->stdout, $actual);
    }

    public function testGetStderr()
    {
        $actual = $this->stdio->getStderr();
        $this->assertSame($this->stderr, $actual);
    }

    public function testOut()
    {
        $expect = 'foo bar baz';
        $this->stdio->out($expect);
        $this->stdout->rewind();
        $actual = $this->stdout->fread(8192);
        $this->assertSame($expect, $actual);
    }

    public function testOutln()
    {
        $expect = 'foo bar baz';
        $this->stdio->outln($expect);
        $this->stdout->rewind();
        $actual = $this->stdout->fread(8192);
        $this->assertSame($expect . PHP_EOL, $actual);
    }

    public function testErr()
    {
        $expect = 'foo bar baz';
        $this->stdio->err($expect);
        $this->stderr->rewind();
        $actual = $this->stderr->fread(8192);
        $this->assertSame($expect, $actual);
    }

    public function testErrln()
    {
        $expect = 'foo bar baz';
        $this->stdio->errln($expect);
        $this->stderr->rewind();
        $actual = $this->stderr->fread(8192);
        $this->assertSame($expect . PHP_EOL, $actual);
    }

    public function testInln()
    {
        $expect = 'foo bar baz' . PHP_EOL;
        $this->stdin->fwrite($expect);
        $this->stdin->rewind();
        $actual = $this->stdio->inln();
        $this->assertSame($expect, $actual);
    }

    public function testIn()
    {
        $expect = 'foo bar baz';
        $this->stdin->fwrite($expect . PHP_EOL);
        $this->stdin->rewind();
        $actual = $this->stdio->in();
        $this->assertSame($expect, $actual);
    }
}
