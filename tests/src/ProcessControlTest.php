<?php
namespace Aura\Cli;

class ProcessControlTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (! extension_loaded('pcntl')) {
            $this->markTestSkipped('The pcntl extension is not available.');
        }
    }

    protected function newProcessControl()
    {
        return new ProcessControl();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf('Aura\\Cli\\ProcessControl', $this->newProcessControl());
    }

    public function testInvoke()
    {
        if (! function_exists('pcntl_signal')) {
            $this->markTestSkipped('The pcntl_signal function is not availalbe.');
            return;
        }

        $this->assertTrue($this->newProcessControl()->__invoke(SIGINT, function(){}));
    }
}
