<?php
namespace Aura\Cli;

/**
 * Test class for Command.
 */
class CommandTest extends \PHPUnit_Framework_TestCase
{
    protected function newMockCommand($argv = [], $class = 'Aura\Cli\MockCommand')
    {
        // standard input/output
        $stdin  = fopen('php://memory', 'r');
        $stdout = fopen('php://memory', 'w+');
        $stderr = fopen('php://memory', 'w+');
        $vt100 = new Vt100;
        $stdio = new Stdio($stdin, $stdout, $stderr, $vt100);
        
        // getopt
        $option_factory = new OptionFactory();
        $getopt = new Getopt($option_factory);
        
        // Command
        $_SERVER['argv'] = $argv;
        $context = new Context;
        return new $class($context, $stdio, $getopt);
    }
    
    public function testExec()
    {
        $expect = ['foo', 'bar', 'baz', 'dib'];
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
}
