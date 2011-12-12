<?php
namespace Aura\Cli;

/**
 * Test class for Context.
 */
class ContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Context
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->object = new Context;
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
     * 
     * Test -- Retrieves an **unfiltered** value by key from the [[$env]] 
     * property, or an alternate default value if that key does not exist.
     * 
     */
    public function testGetEnv()
    {
        // pre-populate the superglobal with fake values for testing
        $_ENV = array(
            'foo' => 'bar',
            'baz' => 'dib',
        );
        
        $context = new Context;
        
        // get a key
        $actual = $context->getEnv('foo');
        $this->assertSame('bar', $actual);
        
        // get a non-existent key
        $actual = $context->getEnv('zim');
        $this->assertNull($actual);
        
        // get a non-existent key with default value
        $actual = $context->getEnv('zim', 'gir');
        $this->assertSame('gir', $actual);
        
        // get the whole env
        $actual = $context->getEnv();
        $this->assertSame($_ENV, $actual);
    }

    /**
     * 
     * Test -- Retrieves an **unfiltered** value by key from the [[$server]] 
     * property, or an alternate default value if that key does not exist.
     * 
     */
    public function testGetServer()
    {
        // pre-populate the superglobal with fake values for testing
        $_SERVER = array(
            'foo' => 'bar',
            'baz' => 'dib',
        );
        
        $context = new Context;
        
        // get a key
        $actual = $context->getServer('foo');
        $this->assertSame('bar', $actual);
        
        // get a non-existent key
        $actual = $context->getServer('zim');
        $this->assertNull($actual);
        
        // get a non-existent key with default value
        $actual = $context->getServer('zim', 'gir');
        $this->assertSame('gir', $actual);
        
        // get the whole env
        $actual = $context->getServer();
        $this->assertSame($_SERVER, $actual);
    }
    
    public function testGetArgv()
    {
        $_SERVER['argv'] = array('foo', 'bar');
        $context = new Context;
        $actual = $context->getArgv();
        $this->assertSame($_SERVER['argv'], $actual);
    }
    
    public function testShiftArgv()
    {
        $_SERVER['argv'] = array('foo', 'bar');
        $context = new Context;
        $actual = $context->getArgv();
        $this->assertSame($_SERVER['argv'], $actual);
        
        $actual = $context->shiftArgv();
        $this->assertSame('foo', $actual);
        
        $actual = $context->getArgv();
        $this->assertSame(array('bar'), $actual);
    }
}
