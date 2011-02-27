<?php
namespace aura\cli;
use aura\cli\Controller as Controller;

class MockController extends Controller
{
    private $_action;
    
    private $_pre_action;
    
    private $_post_action;
    
    protected $options = array(
        'option' => array(),
    );
    
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
    
    public function action()
    {
        $this->_action = __METHOD__;
        $this->stdio->out($this->_action);
    }
}
