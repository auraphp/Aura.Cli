<?php
namespace Aura\Cli\Config;

use Aura\Framework\Test\WiringAssertionsTrait;

class DefaultTest extends \PHPUnit_Framework_TestCase
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
        $this->assertNewInstance('Aura\Cli\Context');
        $this->assertNewInstance('Aura\Cli\ValuesFactory');
        $this->assertNewInstance('Aura\Cli\Stdio');
    }
}
