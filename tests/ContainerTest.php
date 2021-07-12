<?php
namespace Aura\Cli\_Config;

use Aura\Di\_Config\AbstractContainerTest;

class ContainerTest extends AbstractContainerTest
{
    protected function getConfigClasses()
    {
        return array(
            'Aura\Cli\_Config\Common',
        );
    }

    protected function getAutoResolve()
    {
        return false;
    }

    public function provideNewInstance()
    {
        return array(
            array('Aura\Cli\Context'),
            array('Aura\Cli\Context\Argv'),
            array('Aura\Cli\Context\Env'),
            array('Aura\Cli\Context\GetoptFactory'),
            array('Aura\Cli\Context\GetoptParser'),
            array('Aura\Cli\Context\Server'),
            array('Aura\Cli\Help'),
            array('Aura\Cli\Stdio'),
        );
    }
}
