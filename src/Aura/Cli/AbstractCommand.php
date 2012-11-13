<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @package Aura.Cli
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Cli;

use Exception;
use Aura\Cli\Exception as CliException;

/**
 * 
 * The CLI equivalent of a page-controller to perform a single action.
 * 
 * @package Aura.Cli
 * 
 */
abstract class AbstractCommand
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
    protected $options = [];

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
    protected $params = [];

    /**
     * 
     * The return code when this command is done.
     * 
     * @var int
     * 
     */
    protected $return = 0;
    
    /**
     * 
     * Constructor.
     * 
     * @param \Aura\Cli\Context $context The command-line context.
     * 
     * @param \Aura\Cli\Stdio $stdio Standard input/output streams.
     * 
     * @param \Aura\Cli\Getopt $getopt An options processor and reader.
     * 
     */
    public function __construct(
        Context         $context,
        Stdio           $stdio,
        Getopt          $getopt,
        SignalInterface $signal
    ) {
        $this->context = $context;
        $this->stdio   = $stdio;
        $this->getopt  = $getopt;
        $this->signal  = $signal;
        $this->init();
    }

    /**
     * 
     * Post-constructor initialization.
     * 
     * @return void
     * 
     */
    protected function init()
    {
        // set signal handlers
        $this->signal->handler($this, 'pre_exec',        [$this, 'preExec']);
        $this->signal->handler($this, 'pre_action',      [$this, 'preAction']);
        $this->signal->handler($this, 'post_action',     [$this, 'postAction']);
        $this->signal->handler($this, 'pre_render',      [$this, 'preRender']);
        $this->signal->handler($this, 'post_render',     [$this, 'postRender']);
        $this->signal->handler($this, 'post_exec',       [$this, 'postExec']);
        
        // the exception-catching signal handler on this class is intended as
        // a final fallback; other handlers most likely need to run before it.
        $this->signal->handler(
            $this,
            'catch_exception',
            [$this, 'catchException'],
            999
        );
        
        // initialize getopt
        $this->getopt->init($this->options, $this->options_strict);
    }
    
    /**
     * 
     * Executes the Command.  In order, it does these things:
     * 
     * - signals `pre_exec`, thereby calling `preExec()`
     * 
     * - loads $getopt and sets $params
     * 
     * - signals `pre_action`, thereby calling `preAction()`
     * 
     * - calls `action()`
     * 
     * - signals `post_action`, thereby calling `postAction()`
     * 
     * - signals `post_exec`, thereby calling `postExec()`
     * 
     * - signals `catch_exception` when a exception is thrown, thereby
     *   calling `catchException()`
     * 
     * - resets the terminal to normal
     * 
     * - returns the `$return` code
     * 
     * @see action()
     * 
     * @return void
     * 
     */
    public function exec()
    {
        try {
            
            // pre-exec
            $this->signal->send($this, 'pre_exec', $this);
            
            // load getopt, then set params. we need to do this here so that
            // exceptions thrown by getopt are signaled to handlers.
            $this->getopt->load($this->context->getArgv());
            $this->params = $this->getopt->getParams();
            
            // pre-action, action, post-action
            $this->signal->send($this, 'pre_action', $this);
            $return = $this->action();
            if ($return !== null) {
                $this->return = $return;
            }
            $this->signal->send($this, 'post_action', $this);
            
            // post-exec
            $this->signal->send($this, 'post_exec', $this);
            
        } catch (Exception $exception) {
            
            // set the exception and send a signal
            $this->exception = $exception;
            $this->signal->send($this, 'catch_exception', $this);
            
        }

        // reset terminal output to normal colors
        $this->stdio->out("%n");
        $this->stdio->err("%n");
        
        // send back the return code, and done
        return $this->getReturn();
    }

    public function getException()
    {
        return $this->exception;
    }
    
    protected function setReturn($return)
    {
        $this->return = (int) $return;
    }
    
    public function getReturn()
    {
        return $this->return;
    }
    
    /**
     * 
     * Runs at the beginning of `exec()` before `preAction()`.
     * 
     * @return void
     * 
     */
    public function preExec()
    {
    }

    /**
     * 
     * Runs before `action()` but after `preExec()`.
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
    abstract protected function action();

    /**
     * 
     * Runs after `action()` but before `postExec()`.
     * 
     * @return mixed
     * 
     */
    public function postAction()
    {
    }

    /**
     * 
     * Runs at the end of `exec()` after `postAction()`.
     * 
     * @return mixed
     * 
     */
    public function postExec()
    {
    }
    
    /**
     * 
     * Runs when `exec()` catches an exception.
     * 
     * @return mixed
     * 
     */
    public function catchException()
    {
        // get the current exception
        $e = $this->getException();
        
        // is this a message-only exception?
        $message_only = $e instanceof CliException && $e->getMessageOnly();
        if ($message_only) {
            
            // print the message to stderr
            $this->stdio->errln($e->getMessage());
            
            // set the return code
            $this->setReturn($e->getCode());
            
            // done
            return;
            
        }
        
        // not a message-only exception. throw a copy, with the original as
        // the previous exception so that we can see a full trace.
        $class = get_class($e);
        throw new $class($e->getMessage(), $e->getCode(), $e);
    }
}
