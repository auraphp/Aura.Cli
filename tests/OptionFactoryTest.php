<?php
namespace Aura\Cli;

/**
 * Test class for OptionFactory.
 */
class OptionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OptionFactory
     */
    protected $factory;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->factory = new OptionFactory();
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
        $params = array(
            'name' => 'foo',
        );
        
        $option = $this->factory->newInstance($params);
        
        $this->assertType('Aura\Cli\Option', $option);
    }
}
