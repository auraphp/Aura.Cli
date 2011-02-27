<?php
namespace aura\cli;

/**
 * Test class for OptionFactory.
 */
class OptionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OptionFactory
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->object = new OptionFactory();
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
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
