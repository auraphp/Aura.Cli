<?php
namespace Aura\Cli;

/**
 * Test class for Vt100.
 */
class Vt100Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Vt100
     */
    protected $vt100;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->vt100 = new Vt100;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     */
    public function testFormat()
    {
        $text   = '%Kbold%%percent%n';
        $esc    = chr(27);
        $expect = "{$esc}[30;1mbold%percent{$esc}[0m";
        $actual = $this->vt100->format($text);
        $this->assertSame($expect, $actual);
    }

    public function testStrip()
    {
        $text   = '%Kbold%%percent%n';
        $expect = "bold%percent";
        $actual = $this->vt100->strip($text);
        $this->assertSame($expect, $actual);
    }

    public function testSetAndGetPosix()
    {
        $list = [true, false, null];
        foreach ($list as $flag) {
            $this->vt100->setPosix($flag);
            $this->assertSame($flag, $this->vt100->getPosix());
        }
    }

    public function testSetAndGetPhpOs()
    {
        $actual = $this->vt100->getPhpOs();
        $this->assertSame(PHP_OS, $actual);

        $this->vt100->setPhpOs('win');
        $actual = $this->vt100->getPhpOs();
        $this->assertSame('win', $actual);
    }

    public function testWrite()
    {
        $text = '%Kbold%%percent%n';
        $expect = "bold%percent";

        $handle = fopen('php://memory', 'w+');
        $this->vt100->write($handle, $text);
        rewind($handle);
        $actual = fread($handle, 8192);
        fclose($handle);

        $this->assertSame($expect, $actual);
    }

    public function testWriteln()
    {
        $text = '%Kbold%%percent%n';
        $expect = "bold%percent" . PHP_EOL;

        $handle = fopen('php://memory', 'w+');
        $this->vt100->writeln($handle, $text);
        rewind($handle);
        $actual = fread($handle, 8192);
        fclose($handle);

        $this->assertSame($expect, $actual);
    }

    public function testWrite_posix()
    {
        $this->vt100->setPosix(true);

        $text   = '%Kbold%%percent%n';
        $esc    = chr(27);
        $expect = "{$esc}[30;1mbold%percent{$esc}[0m";

        $handle = fopen('php://memory', 'w+');
        $this->vt100->write($handle, $text);
        rewind($handle);
        $actual = fread($handle, 8192);
        fclose($handle);

        $this->assertSame($expect, $actual);
    }

    public function testWriteln_posix()
    {
        $this->vt100->setPosix(true);

        $text   = '%Kbold%%percent%n';
        $esc    = chr(27);
        $expect = "{$esc}[30;1mbold%percent{$esc}[0m" . PHP_EOL;

        $handle = fopen('php://memory', 'w+');
        $this->vt100->writeln($handle, $text);
        rewind($handle);
        $actual = fread($handle, 8192);
        fclose($handle);

        $this->assertSame($expect, $actual);
    }

    public function testWrite_win()
    {
        $this->vt100->setPhpOs('win');

        $text = '%Kbold%%percent%n';
        $expect = "bold%percent";

        $handle = fopen('php://memory', 'w+');
        $this->vt100->write($handle, $text);
        rewind($handle);
        $actual = fread($handle, 8192);
        fclose($handle);

        $this->assertSame($expect, $actual);
    }

    public function testWriteln_win()
    {
        $this->vt100->setPhpOs('win');

        $text = '%Kbold%%percent%n';
        $expect = "bold%percent" . PHP_EOL;

        $handle = fopen('php://memory', 'w+');
        $this->vt100->writeln($handle, $text);
        rewind($handle);
        $actual = fread($handle, 8192);
        fclose($handle);

        $this->assertSame($expect, $actual);
    }

}
