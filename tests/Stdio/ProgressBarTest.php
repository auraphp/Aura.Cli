<?php
namespace Aura\Cli\Stdio;

use Aura\Cli\Stdio;

class ProgressBarTest extends \PHPUnit\Framework\TestCase
{
    protected $stdout;

    protected $stdio;

    protected $time;

    protected function setUp(): void
    {
        parent::setUp();
        $this->time = 1000.0;
    }

    protected function newProgressBar($total, $tty, $width = 10)
    {
        $this->stdout = new Handle('php://memory', 'w+', null, null, $tty);
        $this->stdio = new Stdio(
            new Handle('php://memory', 'r+'),
            $this->stdout,
            new Handle('php://memory', 'w+'),
            new Formatter
        );

        $time = &$this->time;
        $clock = function () use (&$time) {
            return $time;
        };

        return new ProgressBar($this->stdio, $total, $width, $clock);
    }

    protected function getOutput()
    {
        $this->stdout->rewind();
        return $this->stdout->fread();
    }

    public function testStartDrawsEmptyBar()
    {
        $bar = $this->newProgressBar(30, true);
        $bar->start();

        $expect = "\r[>         ] 0%  0/30  elapsed 0:00  left --";
        $this->assertSame($expect, $this->getOutput());
    }

    public function testAdvanceRedrawsWithRemainingAndElapsed()
    {
        $bar = $this->newProgressBar(30, true);
        $bar->start();
        $this->time += 10;
        $bar->advance(10);

        $expect = "\r[>         ] 0%  0/30  elapsed 0:00  left --"
                . "\r[===>      ] 33%  10/30  elapsed 0:10  left 0:20";
        $this->assertSame($expect, $this->getOutput());
        $this->assertSame(10, $bar->getProgress());
    }

    public function testShorterLineIsPaddedToClearThePreviousOne()
    {
        $bar = $this->newProgressBar(2, true);
        $bar->start();
        $this->time += 100;
        $bar->advance();
        $long = "[=====>    ] 50%  1/2  elapsed 1:40  left 1:40";
        $bar->setProgress(0);
        $short = "[>         ] 0%  0/2  elapsed 1:40  left --";

        $output = $this->getOutput();
        $lines = explode("\r", $output);
        $this->assertSame($long, $lines[2]);
        $this->assertSame(
            $short . str_repeat(' ', strlen($long) - strlen($short)),
            $lines[3]
        );
    }

    public function testFinishDrawsFullBarAndNewline()
    {
        $bar = $this->newProgressBar(4, true);
        $bar->start();
        $this->time += 8;
        $bar->finish();

        $output = $this->getOutput();
        $expect = "\r[==========] 100%  4/4  elapsed 0:08  left 0:00"
                . PHP_EOL;
        $this->assertStringEndsWith($expect, $output);
        $this->assertSame(4, $bar->getProgress());
    }

    public function testFinishTwiceWritesOnce()
    {
        $bar = $this->newProgressBar(4, true);
        $bar->start();
        $bar->finish();
        $once = $this->getOutput();
        $bar->finish();
        $this->assertSame($once, $this->getOutput());
    }

    public function testNonTerminalSkipsRedrawsAndPrintsFinalLine()
    {
        $bar = $this->newProgressBar(3, false);
        $bar->start();
        $bar->advance();
        $bar->advance();
        $this->assertSame('', $this->getOutput());

        $this->time += 6;
        $bar->finish();
        $expect = "[==========] 100%  3/3  elapsed 0:06  left 0:00"
                . PHP_EOL;
        $this->assertSame($expect, $this->getOutput());
    }

    public function testAdvanceWithoutStartStartsTheBar()
    {
        $bar = $this->newProgressBar(10, true);
        $bar->advance(5);

        $output = $this->getOutput();
        $this->assertSame(2, substr_count($output, "\r"));
        $this->assertStringEndsWith(
            "[=====>    ] 50%  5/10  elapsed 0:00  left 0:00",
            $output
        );
    }

    public function testProgressIsClamped()
    {
        $bar = $this->newProgressBar(10, false);
        $bar->setProgress(15);
        $this->assertSame(10, $bar->getProgress());
        $bar->setProgress(-5);
        $this->assertSame(0, $bar->getProgress());
    }

    public function testZeroTotal()
    {
        $bar = $this->newProgressBar(0, true);
        $bar->start();
        $this->assertSame(
            "\r[==========] 100%  0/0  elapsed 0:00  left --",
            $this->getOutput()
        );
    }

    public function testSeparateBarsKeepSeparateStartTimes()
    {
        $first = $this->newProgressBar(10, false);
        $first->start();
        $this->time += 50;
        $second = $this->newProgressBar(10, false);
        $second->start();
        $this->time += 5;

        $this->assertStringContainsString('elapsed 0:55', $first->render());
        $this->assertStringContainsString('elapsed 0:05', $second->render());
    }

    public function testRestart()
    {
        $bar = $this->newProgressBar(10, false);
        $bar->start();
        $bar->advance(4);
        $bar->finish();
        $this->time += 30;
        $bar->start();

        $this->assertSame(0, $bar->getProgress());
        $this->assertStringContainsString('elapsed 0:00', $bar->render());
    }

    public function testLongTimes()
    {
        $bar = $this->newProgressBar(4, false);
        $bar->start();
        $this->time += 3725;
        $bar->advance();

        $this->assertStringEndsWith(
            'elapsed 1:02:05  left 3:06:15',
            $bar->render()
        );
    }

    public function testDefaultWidthFitsEightyColumns()
    {
        $stdio = new Stdio(
            new Handle('php://memory', 'r+'),
            new Handle('php://memory', 'w+', null, null, false),
            new Handle('php://memory', 'w+'),
            new Formatter
        );
        $time = 0;
        $clock = function () use (&$time) {
            return $time;
        };
        $bar = new ProgressBar($stdio, 99999, 20, $clock);
        $bar->start();
        $time = 35999;
        $bar->advance();

        $this->assertLessThan(80, strlen($bar->render()));
    }

    public function testPosixWithoutTtyDoesNotRedraw()
    {
        $this->stdout = new Handle('php://memory', 'w+', null, true, false);
        $stdio = new Stdio(
            new Handle('php://memory', 'r+'),
            $this->stdout,
            new Handle('php://memory', 'w+'),
            new Formatter
        );
        $bar = new ProgressBar($stdio, 2);
        $bar->start();
        $bar->advance();
        $this->assertSame('', $this->getOutput());
    }

    public function testGetTotal()
    {
        $bar = $this->newProgressBar(7, false);
        $this->assertSame(7, $bar->getTotal());
    }

    public function testNegativeTotal()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->newProgressBar(-1, false);
    }

    public function testZeroWidth()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->newProgressBar(10, false, 0);
    }

    public function testDefaultClock()
    {
        $stdio = new Stdio(
            new Handle('php://memory', 'r+'),
            new Handle('php://memory', 'w+', null, null, false),
            new Handle('php://memory', 'w+'),
            new Formatter
        );
        $bar = new ProgressBar($stdio, 2);
        $bar->start();
        $bar->advance();

        $this->assertStringStartsWith(
            '[==========>         ] 50%  1/2',
            $bar->render()
        );
    }
}
