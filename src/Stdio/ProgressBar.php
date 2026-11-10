<?php
/**
 *
 * This file is part of Aura for PHP.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
namespace Aura\Cli\Stdio;

use Aura\Cli\Stdio;
use InvalidArgumentException;

/**
 *
 * Draws a progress bar on standard output, such as:
 *
 *     [==========>                   ] 33%  10/30 remaining: 20 sec.  elapsed: 10 sec.
 *
 * On a POSIX terminal the bar is redrawn in place on each update. Otherwise
 * (for example when output is piped to a file) the updates are skipped and
 * only the final line is printed by `finish()`.
 *
 * @package Aura.Cli
 *
 */
class ProgressBar
{
    /**
     *
     * The Stdio object to write to.
     *
     * @var Stdio
     *
     */
    protected $stdio;

    /**
     *
     * The number of steps that make up the whole task.
     *
     * @var int
     *
     */
    protected $total;

    /**
     *
     * The number of characters between the brackets of the bar.
     *
     * @var int
     *
     */
    protected $width;

    /**
     *
     * Returns the current time in seconds.
     *
     * @var callable
     *
     */
    protected $clock;

    /**
     *
     * The time start() was called, or null when not started.
     *
     * @var float|null
     *
     */
    protected $start_time;

    /**
     *
     * The number of steps done so far.
     *
     * @var int
     *
     */
    protected $progress = 0;

    /**
     *
     * Has finish() been called?
     *
     * @var bool
     *
     */
    protected $finished = false;

    /**
     *
     * The length of the last line drawn, used to clear leftover characters.
     *
     * @var int
     *
     */
    protected $last_length = 0;

    /**
     *
     * Constructor.
     *
     * @param Stdio $stdio Write the bar to this Stdio's standard output.
     *
     * @param int $total The number of steps that make up the whole task.
     *
     * @param int $width The number of characters between the brackets.
     *
     * @param callable $clock Returns the current time in seconds; defaults
     * to `microtime(true)`. Generally for testing.
     *
     * @throws InvalidArgumentException When $total is negative or $width is
     * less than 1.
     *
     */
    public function __construct(
        Stdio $stdio,
        $total,
        $width = 30,
        ?callable $clock = null
    ) {
        $total = (int) $total;
        $width = (int) $width;

        if ($total < 0) {
            throw new InvalidArgumentException(
                "The progress bar total must not be negative; got {$total}."
            );
        }

        if ($width < 1) {
            throw new InvalidArgumentException(
                "The progress bar width must be at least 1; got {$width}."
            );
        }

        $this->stdio = $stdio;
        $this->total = $total;
        $this->width = $width;
        $this->clock = $clock ?: function () {
            return microtime(true);
        };
    }

    /**
     *
     * Starts (or restarts) the bar at zero and draws it.
     *
     * @return null
     *
     */
    public function start()
    {
        $this->start_time = $this->now();
        $this->progress = 0;
        $this->finished = false;
        $this->last_length = 0;
        $this->draw();
    }

    /**
     *
     * Advances the bar by a number of steps and redraws it; starts the bar
     * if it has not been started.
     *
     * @param int $step The number of steps to advance.
     *
     * @return null
     *
     */
    public function advance($step = 1)
    {
        $this->setProgress($this->progress + (int) $step);
    }

    /**
     *
     * Sets the number of steps done and redraws the bar; starts the bar if
     * it has not been started. The value is kept between 0 and the total.
     *
     * @param int $progress The number of steps done.
     *
     * @return null
     *
     */
    public function setProgress($progress)
    {
        if ($this->start_time === null) {
            $this->start();
        }

        $this->progress = max(0, min($this->total, (int) $progress));
        $this->draw();
    }

    /**
     *
     * Completes the bar, draws it at 100%, and ends the line. Calling it
     * again does nothing until the bar is restarted.
     *
     * @return null
     *
     */
    public function finish()
    {
        if ($this->finished) {
            return;
        }

        if ($this->start_time === null) {
            $this->start_time = $this->now();
        }

        $this->progress = $this->total;
        $this->finished = true;

        if ($this->isTerminal()) {
            $this->draw();
            $this->stdio->outln();
            return;
        }

        $this->stdio->outln($this->render());
    }

    /**
     *
     * Returns the number of steps done so far.
     *
     * @return int
     *
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     *
     * Returns the number of steps that make up the whole task.
     *
     * @return int
     *
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     *
     * Returns the progress bar line for the current state, without any
     * carriage return or line ending.
     *
     * @return string
     *
     */
    public function render()
    {
        $ratio = $this->total > 0 ? $this->progress / $this->total : 1;
        $filled = (int) floor($ratio * $this->width);

        if ($filled < $this->width) {
            $bar = str_repeat('=', $filled)
                 . '>'
                 . str_repeat(' ', $this->width - $filled - 1);
        } else {
            $bar = str_repeat('=', $this->width);
        }

        $percent = (int) floor($ratio * 100);
        $elapsed = $this->start_time === null
                 ? 0
                 : max(0, $this->now() - $this->start_time);

        if ($this->progress > 0) {
            $remaining = (int) round(
                $elapsed / $this->progress * ($this->total - $this->progress)
            );
            $remaining .= ' sec.';
        } else {
            $remaining = '--';
        }

        return "[{$bar}] {$percent}%  {$this->progress}/{$this->total}"
             . " remaining: {$remaining}  elapsed: " . (int) $elapsed . ' sec.';
    }

    /**
     *
     * Redraws the bar in place, but only on a terminal.
     *
     * @return null
     *
     */
    protected function draw()
    {
        if (! $this->isTerminal()) {
            return;
        }

        $line = $this->render();
        $length = strlen($line);
        $padding = str_repeat(' ', max(0, $this->last_length - $length));
        $this->last_length = $length;
        $this->stdio->out("\r" . $line . $padding);
    }

    /**
     *
     * Is standard output an interactive terminal?
     *
     * @return bool
     *
     */
    protected function isTerminal()
    {
        return (bool) $this->stdio->getStdout()->isPosix();
    }

    /**
     *
     * Returns the current time in seconds from the clock.
     *
     * @return float
     *
     */
    protected function now()
    {
        return (float) call_user_func($this->clock);
    }
}
