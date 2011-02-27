<?php
namespace aura\cli;
use aura\di\Forge as Forge;
use aura\di\Config as Config;
use aura\signal\Manager;
use aura\signal\HandlerFactory;
use aura\signal\ResultFactory;
use aura\signal\ResultCollection;

/**
 * Test class for Dispatcher.
 */
class ControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        
        // context
        $_SERVER = array();
        $context = new Context;
        
        // standard input/output
        $stdin  = fopen('php://memory', 'r');
        $stdout = fopen('php://memory', 'w+');
        $stderr = fopen('php://memory', 'w+');
        $vt100 = new Vt100;
        $stdio = new Stdio($stdin, $stdout, $stderr, $vt100);
        
        // getopt
        $option_factory = new OptionFactory();
        $getopt = new Getopt($option_factory);
        
        // signals
        $signal = new Manager(new HandlerFactory, new ResultFactory, new ResultCollection);
        
        // set up the forge
        $this->forge = new Forge(new Config);
        $params = $this->forge->getConfig()->getParams();
        $params['aura\cli\Controller'] = array(
            'context' => $context,
            'stdio'   => $stdio,
            'getopt'  => $getopt,
            'signal'  => $signal,
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
     * @todo Implement testNewInstance().
     */
    public function testNewInstance()
    {
        $controllers = array('mock' => 'aura\cli\MockController');
        $not_found = null;
        $factory = new ControllerFactory($this->forge, $controllers, $not_found);
        
        $controller = $factory->newInstance('mock');
        $this->assertType('aura\cli\MockController', $controller);
    }
    
    public function testNewInstance_notFound()
    {
        $controllers = array();
        $not_found = 'aura\cli\MockController';
        $factory = new ControllerFactory($this->forge, $controllers, $not_found);
        
        $controller = $factory->newInstance('mock');
        $this->assertType('aura\cli\MockController', $controller);
    }
    
    /**
     * @expectedException \aura\cli\Exception
     */
    public function testNewInstance_notFoundAndNoDefault()
    {
        $controllers = array();
        $not_found = null;
        $factory = new ControllerFactory($this->forge, $controllers, $not_found);
        $controller = $factory->newInstance('mock');
    }
}
