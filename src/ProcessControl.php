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

use Aura\Cli\Exception\ExtensionNotAvailable;
use Aura\Cli\Exception\FunctionNotAvailable;
use Aura\Cli\Exception\SignalNotCatchable;

/**
 * Helper for dealing with pcntl signal functions.
 *
 * @package Aura.Cli
 */
class ProcessControl
{
    /**
     * List of catchable signals
     *
     * @var array
     */
    protected $catchable_signals = array();

    /**
     * The construct will check for any required function that may not be set. Exception is thrown
     * if the functions are not available. The catchable signals list is set after this, if it's
     * done before and pcntl isn't installed we get a load more errors from the constant not
     * existing.
     */
    public function __construct()
    {
        if (! extension_loaded('pcntl')) {
            throw new ExtensionNotAvailable('The pcntl extension is not available.');
        }

        $required_functions = array(
            'pcntl_signal'
        );

        foreach ($required_functions as $required_function) {
            if (! function_exists($required_function)) {
                throw new FunctionNotAvailable(
                    sprintf("The required function '%s' does not exist.", $required_function)
                );
            }
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
     * Check if the signal is catchable, then set the signal to be caught by $callable
     *
     * @param int $signal
     * @param callable|int $handler
     * @param bool $restart_syscalls
     *
     * @return bool
     * @throws Exception\SignalNotCatchable
     * @throws \InvalidArgumentException
     */
    public function __invoke($signal, $handler, $restart_syscalls = true)
    {
        if (! array_key_exists($signal, $this->catchable_signals)) {
            throw new SignalNotCatchable(sprintf("The signal '%d' is not catchable.", $signal));
        };

        if (! is_callable($handler) && ! is_int($handler)) {
            throw new \InvalidArgumentException('handler must be of type callable or int.');
        }

        return $this->catchSignal($signal, $handler, $restart_syscalls);
    }

    /**
     * @param int $signal
     * @param callable|int $handler
     * @param bool $restart_syscalls
     *
     * @return bool
     */
    protected function catchSignal($signal, $handler, $restart_syscalls)
    {
        return pcntl_signal($signal, $handler, $restart_syscalls);
    }
}
