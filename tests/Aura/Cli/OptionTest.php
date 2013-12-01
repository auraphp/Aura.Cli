<?php
namespace Aura\Cli;

/**
 * Test class for Option.
 */
class OptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Builder
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

    public function testInit()
    {
        $spec = [
            'name' => 'foo_bar',
            'long' => 'foo-bar',
            'short' => 'f',
        ];
        
        $option = $this->factory->newInstance($spec);
        
        $this->assertSame('foo_bar',    $option->getName());
        $this->assertSame('foo-bar',    $option->getLong());
        $this->assertSame('f',          $option->getShort());
    }
    
    /**
     * @expectedException Aura\Cli\Exception\OptionName
     */
    public function testInitNoName()
    {
        $spec = [
            'long' => 'foo-bar',
            'short' => 'f',
        ];
        
        $option = $this->factory->newInstance($spec);
    }
    
    public function testInitNoLongOrShort()
    {
        $spec = [
            'name' => 'foo_bar',
        ];
        
        $option = $this->factory->newInstance($spec);
        
        $this->assertSame('foo_bar',    $option->getName());
        $this->assertSame('foo-bar',    $option->getLong());
        $this->assertSame('',           $option->getShort());
    }
    
    
    /**
     * @expectedException Aura\Cli\Exception\OptionParam
     */
    public function testInitBadParamValue()
    {
        $spec = [
            'name' => 'foo_bar',
            'param' => 'no_such_type',
        ];
        
        $option = $this->factory->newInstance($spec);
    }
    
    
    public function testSetValue()
    {
        $spec = [
            'name' => 'foo_bar',
            'long' => 'foo-bar',
            'short' => 'f',
        ];
        
        $option = $this->factory->newInstance($spec);
        
        $option->setValue('zim');
        
        $this->assertSame('zim', $option->getValue());
    }
    
    public function testIsMulti()
    {
        $spec = [
            'name' => 'foo_bar',
            'long' => 'foo-bar',
            'short' => 'f',
            'multi' => true,
        ];
        
        $option = $this->factory->newInstance($spec);
        
        $option->setValue('zim');
        $option->setValue('gir');
        
        $this->assertSame(['zim', 'gir'], $option->getValue());
    }
    
    public function testGetValue()
    {
        $spec = [
            'name' => 'foo_bar',
            'long' => 'foo-bar',
            'short' => 'f',
        ];
        
        $option = $this->factory->newInstance($spec);
        
        $option->setValue('zim');
        
        $this->assertSame('zim', $option->getValue());
    }

    public function testGetValueDefault()
    {
        $spec = [
            'name' => 'foo_bar',
            'long' => 'foo-bar',
            'short' => 'f',
            'default' => 'default_value'
        ];
        
        $option = $this->factory->newInstance($spec);
        
        $this->assertSame('default_value', $option->getValue());
    }

    public function testGetDefault()
    {
        $spec = [
            'name' => 'foo_bar',
            'long' => 'foo-bar',
            'short' => 'f',
            'default' => 'default_value'
        ];
        
        $option = $this->factory->newInstance($spec);
        
        $this->assertSame('default_value', $option->getDefault());
    }

    public function testIsParamRequired()
    {
        $spec = [
            'name' => 'foo_bar',
            'long' => 'foo-bar',
            'short' => 'f',
            'param' => Option::PARAM_REQUIRED,
        ];
        
        $option = $this->factory->newInstance($spec);
        
        $this->assertTrue($option->isParamRequired());
        $this->assertFalse($option->isParamRejected());
        $this->assertFalse($option->isParamOptional());
    }

    public function testIsParamRejected()
    {
        $spec = [
            'name' => 'foo_bar',
            'long' => 'foo-bar',
            'short' => 'f',
            'param' => Option::PARAM_REJECTED,
        ];
        
        $option = $this->factory->newInstance($spec);
        
        $this->assertFalse($option->isParamRequired());
        $this->assertTrue($option->isParamRejected());
        $this->assertFalse($option->isParamOptional());
    }

    public function testIsParamOptional()
    {
        $spec = [
            'name' => 'foo_bar',
            'long' => 'foo-bar',
            'short' => 'f',
            'param' => Option::PARAM_OPTIONAL,
        ];
        
        $option = $this->factory->newInstance($spec);
        
        $this->assertFalse($option->isParamRequired());
        $this->assertFalse($option->isParamRejected());
        $this->assertTrue($option->isParamOptional());
    }
}
