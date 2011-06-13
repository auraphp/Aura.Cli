<?php
namespace Aura\Cli;
class MockCommandSkip extends MockCommand
{
    public function preAction()
    {
        parent::preAction();
        $this->_pre_action = true;
        $this->skipAction();
    }
}
