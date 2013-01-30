<?php
namespace Aura\Cli;

use Aura\Framework\Test\WiringAssertionsTrait;

class WiringTest extends \PHPUnit_Framework_TestCase
{
    use WiringAssertionsTrait;
    
    protected function setUp()
    {
        $this->loadDi();
    }
    
    public function testServices()
    {
        $this->assertGet('cli_context', 'Aura\Cli\Context');
        $this->assertGet('cli_stdio', 'Aura\Cli\Stdio');
    }
    
    public function testInstances()
    {
        $this->assertNewInstance('Aura\Cli\AbstractCommand', 'Aura\Cli\MockCommand');
        $this->assertNewInstance('Aura\Cli\Context');
        $this->assertNewInstance('Aura\Cli\ExceptionFactory');
        $this->assertNewInstance('Aura\Cli\Getopt');
        $this->assertNewInstance('Aura\Cli\Stdio');
    }
    
    public function testTranslatedExceptions()
    {
        $factory   = $this->di->newInstance('Aura\Cli\ExceptionFactory');
        $exception = $factory->newInstance('ERR_OPTION_NOT_DEFINED');
        $expect    = "The option '{option}' is not recognized.";
        $actual    = $exception->getMessage();
        $this->assertSame($expect, $actual);
    }
}
