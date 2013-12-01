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

/**
 *
 * A basic signal manager for executing controller hooks.
 *
 * @package Aura.Cli
 *
 */
class Signal implements SignalInterface
{
    /**
     *
     * The signal handlers to be executed.
     *
     * @var array
     *
     */
    protected $handlers = [];

    /**
     *
     * Adds a handler to the list.
     *
     * @param object $origin The object sending the signal.
     *
     * @param string $signal The signal being sent.
     *
     * @param callable $callback The callback to execute when the signal
     * is sent.
     *
     * @return void
     *
     */
    public function handler($origin, $signal, $callback)
    {
        $this->handlers[$signal] = $callback;
    }

    /**
     *
     * Sends a signal to the handlers.
     *
     * @param object $origin The object sending the signal.
     *
     * @param string $signal The signal being sent.
     *
     * @return void
     *
     */
    public function send($origin, $signal)
    {
        $args = func_get_args();
        array_pop($args);
        array_pop($args);
        $func = $this->handlers[$signal];
        call_user_func_array($func, $args);
    }
}
