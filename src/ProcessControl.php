<?php
/**
 *
 * This file is part of Aura for PHP.
 *
 * @package Aura.Cli
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
namespace Aura\Cli;

use Aura\Cli\Exception;
use InvalidArgumentException;

/**
 * Helper for dealing with pcntl signal functions.
 *
 * @package Aura.Cli
 */
class ProcessControl
{
    /**
     * List of catchable signals.
     * 
     * We set them in the constructor, not here, because if pcntl isn't
     * installed then the constants won't exist.
     *
     * @var array
     */
    protected $catchable_signals = array();

    /**
     * 
     * Constructor.
     * 
     */
    public function __construct()
    {
        if (! extension_loaded('pcntl')) {
            throw new Exception\ExtensionNotAvailable('pcntl');
        }

        if (! function_exists('pcntl_signal')) {
            throw new Exception\FunctionNotAvailable('pcntl_signal');
        }
        
        $this->catchable_signals = array(
            SIGHUP => SIGHUP,
            SIGINT => SIGINT,
            SIGUSR1 => SIGUSR1,
            SIGUSR2 => SIGUSR2,
            SIGQUIT => SIGQUIT,
            SIGILL => SIGILL,
            SIGABRT => SIGABRT,
            SIGFPE => SIGFPE,
            SIGSEGV => SIGSEGV,
            SIGPIPE => SIGPIPE,
            SIGALRM => SIGALRM,
            SIGTERM => SIGTERM,
            SIGCHLD => SIGCHLD,
            SIGCONT => SIGCONT,
            SIGTSTP => SIGTSTP,
            SIGTTIN => SIGTTIN,
            SIGTTOU => SIGTTOU,
        );
    }

    /**
     * Check if the signal is catchable, then set the signal to be caught by $handler.
     *
     * @param int $signal The signal to be caught.
     * 
     * @param callable|int $handler The handler to be use for the signal.
     * 
     * @param bool $restart_syscalls
     *
     * @return bool
     * 
     * @throws Exception\SignalNotCatchable
     * 
     * @throws InvalidArgumentException
     * 
     */
    public function __invoke($signal, $handler, $restart_syscalls = true)
    {
        if (! array_key_exists($signal, $this->catchable_signals)) {
            throw new Exception\SignalNotCatchable($signal);
        }

        if (! is_callable($handler) && ! is_int($handler)) {
            throw new InvalidArgumentException('Handler must be of type callable or int.');
        }

        return $this->catchSignal($signal, $handler, $restart_syscalls);
    }

    /**
     * 
     * Catches a signal.
     * 
     * @param int $signal
     * 
     * @param callable|int $handler
     * 
     * @param bool $restart_syscalls
     * 
     * @return bool
     * 
     */
    protected function catchSignal($signal, $handler, $restart_syscalls)
    {
        return pcntl_signal($signal, $handler, $restart_syscalls);
    }
}
