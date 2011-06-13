<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Cli;
use Aura\Signal\Manager as SignalManager;

/**
 * 
 * The CLI equivalent of a page-controller to perform a single action.
 * 
 * @package Aura.Cli
 * 
 */
abstract class Command
{
    /**
     * 
     * A Getopt object for the Command; retains the short and long options
     * passed at the command line.
     * 
     * @var Aura\Cli\Getopt
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
     * Should Getopt be strict about how options are processed?  In strict
     * mode, passing an undefined option will throw an exception; in
     * non-strict, it will not.
     * 
     * @var bool
     * 
     */
    protected $options_strict = Getopt::STRICT;
    
    /**
     * 
     * The positional (numeric) arguments passed at the command line.
     * 
     * @var array
     * 
     */
    protected $params = array();
    
    /**
     * 
     * When set to `true` before `action()` is called, the `action()` will not
     * be called after all.
     * 
     * @var bool
     * 
     */
    protected $skip_action = false;
    
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
        $this->signal->handler($this, 'pre_exec', array($this, 'preExec'));
        $this->signal->handler($this, 'pre_action', array($this, 'preAction'));
        $this->signal->handler($this, 'post_action', array($this, 'postAction'));
        $this->signal->handler($this, 'post_exec', array($this, 'postExec'));
        
        // load the getopt and params properties
        $this->loadGetoptParams();
    }
    
    /**
     * 
     * Passes the Context arguments to `$getopt` and retains the numeric
     * parameters in `$params`.
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
     * Executes the Command.  In order, it does these things:
     * 
     * - signals `'pre_exec'`
     * 
     * - signals `'pre_action'`
     * 
     * - is the action is not to be skipped, calls `action()` and signals 
     *   `'post_action'`
     * 
     * - signals `'post_exec'`
     * 
     * - resets the terminal to normal colors
     * 
     * @signal 'pre_exec'
     * 
     * @signal 'pre_action'
     * 
     * @signal 'post_action'
     * 
     * @signal 'post_exec'
     * 
     * @see action()
     * 
     * @return void
     * 
     */
    public function exec()
    {
        $this->signal->send($this, 'pre_exec', $this);
        $this->signal->send($this, 'pre_action', $this);
        if (! $this->isSkipAction()) {
            $this->action();
            $this->signal->send($this, 'post_action', $this);
        }
        $this->signal->send($this, 'post_exec', $this);
        
        // return terminal output to normal colors
        $this->stdio->out("%n");
        $this->stdio->err("%n");
    }
    
    /**
     * 
     * Stops `exec()` from calling `action()` if it has not already done so.
     * 
     * @return void
     * 
     */
    public function skipAction()
    {
        $this->skip_action = true;
    }
    
    /**
     * 
     * Should the call to `action()` be skipped?
     * 
     * @return bool
     * 
     */
    public function isSkipAction()
    {
        return (bool) $this->skip_action;
    }
    
    /**
     * 
     * Runs before `action()` as part of the `'pre_exec'` signal.
     * 
     * @return mixed
     * 
     */
    public function preExec()
    {
    }
    
    /**
     * 
     * Runs before `action()` as part of the `'pre_action'` signal.
     * 
     * @return mixed
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
     * Runs after `action()` as part of the `'post_action'` signal.
     * 
     * @return mixed
     * 
     */
    public function postAction()
    {
    }
    
    /**
     * 
     * Runs after `action()` as part of the `'post_exec'` signal.
     * 
     * @return mixed
     * 
     */
    public function postExec()
    {
    }
}
