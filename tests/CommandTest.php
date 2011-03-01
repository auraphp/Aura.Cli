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
    protected function newMockCommand($argv = array())
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
        return new MockCommand($context, $stdio, $getopt, $signal);
    }
    
    public function testExec()
    {
        $expect = array('foo', 'bar', 'baz', 'dib');
        $Command = $this->newMockCommand($expect);
        $Command->exec();
        
        // did the params get passed in?
        $actual = $Command->params;
        $this->assertSame($expect, $actual);
    }
    
    public function testExec_hooks()
    {
        $Command = $this->newMockCommand();
        $Command->exec();
        $this->assertTrue($Command->_pre_action);
        $this->assertTrue($Command->_post_action);
    }
}
