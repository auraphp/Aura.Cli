<?php
namespace Aura\Cli\_Config;

use Aura\Di\ContainerAssertionsTrait;

class CommonTest extends \PHPUnit_Framework_TestCase
{
    use ContainerAssertionsTrait;

    public function setUp()
    {
        $this->setUpContainer(array(
            'Aura\Cli\_Config\Common',
        ));
    }

    public function test()
    {
        $this->assertNewInstance('Aura\Cli\Context\Argv');
        $this->assertNewInstance('Aura\Cli\Context\Env');
        $this->assertNewInstance('Aura\Cli\Context\Server');
        $this->assertNewInstance('Aura\Cli\Stdio');
    }
}
