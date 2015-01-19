<?php
/**
 *
 * This file is part of Aura for PHP.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
namespace Aura\Cli;

use Aura\Cli\Exception;
use InvalidArgumentException;

/**
 *
 * Helper for dealing with pcntl signal functions.
 *
 * <http://www.tuxradar.com/practicalphp/16/1/0>
 *
 * @package Aura.Cli
 *
 */
class ProcessControl
{
    /**
     *
     * List of catchable signals. We set them in the constructor, not here,
     * because if pcntl isn't available then the constants won't exist.
     *
     * @var array
     *
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
            SIGHUP,
            SIGINT,
            SIGUSR1,
            SIGUSR2,
            SIGQUIT,
            SIGILL,
            SIGABRT,
            SIGFPE,
            SIGSEGV,
            SIGPIPE,
            SIGALRM,
            SIGTERM,
            SIGCHLD,
            SIGCONT,
            SIGTSTP,
            SIGTTIN,
            SIGTTOU,
        );
    }

    /**
     *
     * Check if the signal is catchable, then set the signal to be caught by $handler.
     *
     * @param int $signal The signal to be caught.
     *
     * @param callable|int $handler The handler to be use for the signal; pass
     * the constant SIG_IGN to ignore the signal, or SIG_DFL to restore the
     * default handler for the signal.
     *
     * @param bool $restart_syscalls Specifies whether system call restarting
     * should be used when this signal arrives.
     *
     * @return bool
     *
     * @throws Exception\SignalNotCatchable
     *
     * @throws InvalidArgumentException
     *
     */
    public function handle($signal, $handler, $restart_syscalls = true)
    {
        if (! isset($this->catchable_signals[$signal])) {
            throw new Exception\SignalNotCatchable($signal);
        }

        if (! is_callable($handler) && ! is_int($handler)) {
            throw new InvalidArgumentException('Handler must be of type callable or int.');
        }

        return pcntl_signal($signal, $handler, $restart_syscalls);
    }
}
