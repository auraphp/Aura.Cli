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
 * An interface for signal managers.
 *
 * @package Aura.Cli
 *
 */
interface SignalInterface
{
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
    public function handler($origin, $signal, $callback);

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
    public function send($origin, $signal);
}
