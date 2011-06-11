<?php
namespace Aura\Cli;
use Aura\Di\Forge as Forge;
use Aura\Di\Config as Config;
use Aura\Signal\Manager;
use Aura\Signal\HandlerFactory;
use Aura\Signal\ResultFactory;
use Aura\Signal\ResultCollection;

/**
 * Test class for Dispatcher.
 */
class CommandFactoryTest extends \PHPUnit_Framework_TestCase
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
        $params['Aura\Cli\Command'] = array(
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
        $map = array('mock' => 'Aura\Cli\MockCommand');
        $not_found = null;
        $factory = new CommandFactory($this->forge, $map, $not_found);
        
        $command = $factory->newInstance('mock');
        $this->assertType('Aura\Cli\MockCommand', $command);
    }
    
    public function testNewInstance_notFound()
    {
        $map = array();
        $not_found = 'Aura\Cli\MockCommand';
        $factory = new CommandFactory($this->forge, $map, $not_found);
        
        $command = $factory->newInstance('mock');
        $this->assertType('Aura\Cli\MockCommand', $command);
    }
    
    /**
     * @expectedException \Aura\Cli\Exception
     */
    public function testNewInstance_notFoundAndNoDefault()
    {
        $map = array();
        $not_found = null;
        $factory = new CommandFactory($this->forge, $map, $not_found);
        $command = $factory->newInstance('mock');
    }
    
    public function testMap()
    {
        $map = array();
        $not_found = null;
        $factory = new CommandFactory($this->forge, $map, $not_found);
        $factory->map('mock', 'Aura\Cli\MockCommand');
        $command = $factory->newInstance('mock');
        $this->assertType('Aura\Cli\MockCommand', $command);
    }
}
