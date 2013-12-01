<?php
namespace Aura\Cli;

use Aura\Cli\AbstractCommand;

class MockCommand extends AbstractCommand
{
    private $action;
    
    private $pre_action = false;
    
    private $post_action = false;
    
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
        $this->pre_action = true;
    }
    
    public function postAction()
    {
        parent::postAction();
        $this->post_action = true;
    }
    
    protected function action()
    {
        $this->action = __METHOD__;
        $this->stdio->out($this->_action);
        return 0;
    }
}
