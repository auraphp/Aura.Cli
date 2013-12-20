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
    protected $catchable_signals = [];

    /**
     * The construct will check for any required function that may not be set. Exception is thrown
     * if the functions are not available. The catchable signals list is set after this, if it's
     * done before and pcntl isn't installed we get a load more errors from the constant not
     * existing.
     */
    public function __construct()
    {
        $required_functions = [
            'pcntl_signal'
        ];

        foreach ($required_functions as $required_function) {
            if (! function_exists($required_function)) {
                throw new FunctionNotAvailable(
                    sprintf("The required function '%s' does not exist.", $required_function)
                );
            }
        }

        $this->catchable_signals = [
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
        ];
    }

    /**
     * Check if the signal is catchable, then set the signal to be caught by $callable
     *
     * @param int $signal
     * @param callable $callable
     *
     * @return bool
     * @throws Exception\SignalNotCatchable
     */
    public function __invoke($signal, callable $callable)
    {
        if (! array_key_exists($signal, $this->catchable_signals)) {
            throw new SignalNotCatchable(sprintf("The singal '%d' is not catchable.", $signal));
        };

        return $this->catchSignal($signal, $callable);
    }

    /**
     * @param int $signal
     * @param callable $callable
     *
     * @return bool
     */
    protected function catchSignal($signal, callable $callable)
    {
        return pcntl_signal($signal, $callable);
    }
}
