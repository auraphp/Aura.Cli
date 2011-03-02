<?php
namespace aura\cli;
use aura\signal\Manager;
use aura\signal\HandlerFactory;
use aura\signal\ResultFactory;
use aura\signal\ResultCollection;

/**
 * Test class for Command.
 */
class CommandTest extends \PHPUnit_Framework_TestCase
{
    protected function newMockCommand($argv = array(), $class = 'aura\cli\MockCommand')
    {
        // standard input/output
        $stdin  = fopen('php://memory', 'r');
        $stdout = fopen('php://memory', 'w+');
        $stderr = fopen('php://memory', 'w+');
        $vt100 = new Vt100;
        $stdio = new Stdio($stdin, $stdout, $stderr, $vt100);
        $signal = new Manager(new HandlerFactory, new ResultFactory, new ResultCollection);
        
        // getopt
        $option_factory = new OptionFactory();
        $getopt = new Getopt($option_factory);
        
        // Command
        $_SERVER['argv'] = $argv;
        $context = new Context;
        return new $class($context, $stdio, $getopt, $signal);
    }
    
    public function testExec()
    {
        $expect = array('foo', 'bar', 'baz', 'dib');
        $command = $this->newMockCommand($expect);
        $command->exec();
        
        // did the params get passed in?
        $actual = $command->params;
        $this->assertSame($expect, $actual);
    }
    
    public function testExec_hooks()
    {
        $command = $this->newMockCommand();
        $command->exec();
        $this->assertTrue($command->_pre_action);
        $this->assertTrue($command->_post_action);
    }
    
    public function testExec_skipAction()
    {
        $command = $this->newMockCommand(array(), 'aura\cli\MockCommandSkip');
        $command->exec();
        $this->assertTrue($command->_pre_action);
        $this->assertFalse($command->_post_action);
    }
}
