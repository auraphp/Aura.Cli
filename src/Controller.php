<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace aura\cli;
use aura\signal\Manager as SignalManager;

/**
 * 
 * The CLI equivalent of a page controller to perform the command action.
 * 
 * @package aura.cli
 * 
 */
abstract class Controller
{
    /**
     * 
     * A Getopt object for the Command.
     * 
     * @var aura\cli\Getopt
     * 
     */
    protected $getopt;
    
    /**
     * 
     * The option definitions for the Getopt object.
     * 
     * @var array
     * 
     */
    protected $options = array();
    
    /**
     * 
     * Should we be strict about which options are passed in?
     * 
     * @var bool
     * 
     */
    protected $options_strict = Getopt::STRICT;
    
    /**
     * 
     * The numeric parameters passed to the Command.
     * 
     * @var array
     * 
     */
    protected $params = array();
    
    /**
     * 
     * Constructor.
     * 
     * @param Context $context The command-line context.
     * 
     * @param Stdio $stdio Standard input/output streams.
     * 
     * @param Getopt $getopt An options processor and reader.
     * 
     * @param aura\signal\Manager $signal A signal manager to send signals to.
     * 
     */
    public function __construct(
        Context       $context,
        Stdio         $stdio,
        Getopt        $getopt,
        SignalManager $signal
    ) {
        // marshal into properties
        $this->context = $context;
        $this->stdio   = $stdio;
        $this->getopt  = $getopt;
        $this->signal  = $signal;
        
        // handle these signals
        $this->signal->handler($this, 'pre_action', array($this, 'preAction'));
        $this->signal->handler($this, 'post_action', array($this, 'postAction'));
        
        // load the getopt and params properties
        $this->loadGetoptParams();
    }
    
    /**
     * 
     * Passes the Context arguments to $getopt and retains the numeric
     * parameters in $params.
     * 
     * @return void
     * 
     */
    protected function loadGetoptParams()
    {
        $this->getopt->init($this->options, $this->options_strict);
        $this->getopt->load($this->context->getArgv());
        $this->params = $this->getopt->getParams();
    }
    
    /**
     * 
     * Executes the Command.
     * 
     * - signals 'pre_action'
     * - calls action()
     * - signals 'post_action'
     * 
     * @signal 'pre_action'
     * 
     * @signal 'post_action'
     * 
     * @return void
     * 
     */
    public function exec()
    {
        $this->signal->send($this, 'pre_action');
        $this->action();
        $this->signal->send($this, 'post_action');
        
        // return terminal output to normal colors
        $this->stdio->out("%n");
        $this->stdio->err("%n");
    }
    
    /**
     * 
     * Runs before the action() method.
     * 
     * @return void
     * 
     */
    public function preAction()
    {
    }
    
    /**
     * 
     * The main logic for the Command.
     * 
     * @return void
     * 
     */
    abstract public function action();
    
    /**
     * 
     * Runs after the action() method.
     * 
     * @return void
     * 
     */
    public function postAction()
    {
    }
}
