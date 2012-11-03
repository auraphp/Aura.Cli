<?php
namespace Aura\Cli;

class ExceptionFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $exception_factory;

    protected function setUp()
    {
        $catalog = include dirname(dirname(dirname(__DIR__)))
                 . DIRECTORY_SEPARATOR . 'intl'
                 . DIRECTORY_SEPARATOR . 'catalog.php';
        
        $translator = new Translator('en_US', $catalog);
        
        $this->exception_factory = new ExceptionFactory($translator);
    }
    
    public function testNewInstance()
    {
        $e = $this->exception_factory->newInstance('ERR_GETOPT_INITIALIZED');
        $this->assertInstanceOf('Aura\Cli\Exception\GetoptInitialized', $e);
        $expect = "Getopt is already initialized.";
        $actual = $e->getMessage();
        $this->assertSame($expect, $actual);
        
        $e = $this->exception_factory->newInstance(
            'ERR_OPTION_NOT_DEFINED',
            ['option' => '--option-name']
        );
        $this->assertInstanceOf('Aura\Cli\Exception\OptionNotDefined', $e);
        $expect = "The option '--option-name' is not recognized.";
        $actual = $e->getMessage();
        $this->assertSame($expect, $actual);
    }
}
