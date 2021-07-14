<?php
namespace Aura\Cli;

class CliFactoryTest extends \PHPUnit\Framework\TestCase
{
    protected $cli_factory;

    public function setUp(): void
    {
        $this->cli_factory = new CliFactory;
    }

    public function testNewContext()
    {
        $actual = $this->cli_factory->newContext($GLOBALS);
        $expect = 'Aura\Cli\Context';
        $this->assertInstanceOf($expect, $actual);
    }

    public function testNewStdio()
    {
        $actual = $this->cli_factory->newStdio();
        $expect = 'Aura\Cli\Stdio';
        $this->assertInstanceOf($expect, $actual);
    }
}
