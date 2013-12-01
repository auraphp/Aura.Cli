<?php
namespace Aura\Cli;

use Aura\Cli\AbstractCommand;

class MockCommand extends AbstractCommand
{
    private $_action;
    
    private $_pre_action = false;
    
    private $_post_action = false;
    
    protected $options = [
        'option' => [],
    ];
    
    public function __get($key)
    {
        return $this->$key;
    }
    
    public function preAction()
    {
        parent::preAction();
        $this->_pre_action = true;
    }
    
    public function postAction()
    {
        parent::postAction();
        $this->_post_action = true;
    }
    
    protected function action()
    {
        $this->_action = __METHOD__;
        $this->stdio->out($this->_action);
        return 0;
    }
}
