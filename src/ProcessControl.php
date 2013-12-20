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

class ProcessControl
{
    protected $catchable_signals = [];

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

    public function __invoke($signal, callable $closure)
    {
        if (! array_key_exists($signal, $this->catchable_signals)) {
            throw new SignalNotCatchable(sprintf("The singal '%d' is not catchable.", $signal));
        };
    }

    protected function catchSignal($signal, callable $closure)
    {
        pcntl_signal($signal, $closure);
    }
}
