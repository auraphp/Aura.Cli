<?php
namespace aura\cli;
use aura\signal\Manager;
use aura\signal\HandlerFactory;
use aura\signal\ResultFactory;
use aura\signal\ResultCollection;

/**
 * Test class for Controller.
 */
class ControllerTest extends \PHPUnit_Framework_TestCase
{
    protected function newMockController($argv = array())
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
        
        // controller
        $_SERVER['argv'] = $argv;
        $context = new Context;
        return new MockController($context, $stdio, $getopt, $signal);
    }
    
    public function testExec()
    {
        $expect = array('foo', 'bar', 'baz', 'dib');
        $controller = $this->newMockController($expect);
        $controller->exec();
        
        // did the params get passed in?
        $actual = $controller->params;
        $this->assertSame($expect, $actual);
    }
    
    public function testExec_hooks()
    {
        $controller = $this->newMockController();
        $controller->exec();
        $this->assertTrue($controller->_pre_action);
        $this->assertTrue($controller->_post_action);
    }
}
