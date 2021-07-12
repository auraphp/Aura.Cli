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

    protected function hasDependencies()
    {
        if (! function_exists('pcntl_signal')) {
            $this->markTestSkipped('The pcntl_signal function is not availalbe.');

            return false;
        }

        return true;
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
        if ($this->hasDependencies()) {
            $this->assertTrue($this->newProcessControl()->handle(SIGINT, function(){}));
        }
    }

    public function testExceptionWhenBadSignalPassed()
    {
        if ($this->hasDependencies()) {
            $this->setExpectedException('Aura\\Cli\\Exception\\SignalNotCatchable');
            $this->newProcessControl()->handle(99999, 1);
        }
    }

    public function testExceptionWhenBadParamPassed()
    {
        if ($this->hasDependencies()) {
            $this->setExpectedException('\InvalidArgumentException');
            $this->newProcessControl()->handle(SIGINT, "string");
        }
    }
}
