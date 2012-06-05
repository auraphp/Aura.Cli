<?php
namespace Aura\Cli;

/**
 * Test class for Stdio.
 */
class StdioTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Stdio
     */
    protected $stdio;

    protected $stdin;

    protected $stdout;

    protected $stderr;

    protected $vt100;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->stdin  = fopen('php://memory', 'r+');
        $this->stdout = fopen('php://memory', 'w+');
        $this->stderr = fopen('php://memory', 'w+');
        $this->vt100  = new Vt100;
        $this->stdio = new Stdio(
            $this->stdin,
            $this->stdout,
            $this->stderr,
            $this->vt100
        );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @todo Implement testGetStdin().
     */
    public function testGetStdin()
    {
        $actual = $this->stdio->getStdin();
        $this->assertSame($this->stdin, $actual);
    }

    /**
     * @todo Implement testGetStdout().
     */
    public function testGetStdout()
    {
        $actual = $this->stdio->getStdout();
        $this->assertSame($this->stdout, $actual);
    }

    /**
     * @todo Implement testGetStderr().
     */
    public function testGetStderr()
    {
        $actual = $this->stdio->getStderr();
        $this->assertSame($this->stderr, $actual);
    }

    /**
     * @todo Implement testOut().
     */
    public function testOut()
    {
        $expect = 'foo bar baz';
        $this->stdio->out($expect);
        rewind($this->stdout);
        $actual = fread($this->stdout, 8192);
        $this->assertSame($expect, $actual);
    }

    /**
     * @todo Implement testOutln().
     */
    public function testOutln()
    {
        $expect = 'foo bar baz';
        $this->stdio->outln($expect);
        rewind($this->stdout);
        $actual = fread($this->stdout, 8192);
        $this->assertSame($expect . PHP_EOL, $actual);
    }

    /**
     * @todo Implement testErr().
     */
    public function testErr()
    {
        $expect = 'foo bar baz';
        $this->stdio->err($expect);
        rewind($this->stderr);
        $actual = fread($this->stderr, 8192);
        $this->assertSame($expect, $actual);
    }

    /**
     * @todo Implement testErrln().
     */
    public function testErrln()
    {
        $expect = 'foo bar baz';
        $this->stdio->errln($expect);
        rewind($this->stderr);
        $actual = fread($this->stderr, 8192);
        $this->assertSame($expect . PHP_EOL, $actual);
    }

    public function testInln()
    {
        $expect = 'foo bar baz' . PHP_EOL;
        fwrite($this->stdin, $expect);
        rewind($this->stdin);
        $actual = $this->stdio->inln();
        $this->assertSame($expect, $actual);
    }

    public function testIn()
    {
        $expect = 'foo bar baz';
        fwrite($this->stdin, $expect . PHP_EOL);
        rewind($this->stdin);
        $actual = $this->stdio->in();
        $this->assertSame($expect, $actual);
    }
}
