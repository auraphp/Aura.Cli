<?php
namespace Aura\Cli;

use Aura\Cli\OptionParser;

class HelpTest extends \PHPUnit_Framework_TestCase
{
    protected $help;

    protected $options = array(
        'z,zim*::' => "A zim option.",
        'f,foo' => "A foo option.",
        'b:',
        'irk:',
        'd,doom*' => "A repeatable flag.",
        'bar::' => "A bar option.",
        'baz*::' => "A baz option.",
    );

    protected function setUp()
    {
        $this->help = new Help(new OptionParser);
    }

    public function testSetAndGetOptions()
    {
        $this->help->setOptions($this->options);
        $this->assertSame($this->options, $this->help->getOptions());
    }

    public function testNoHelp()
    {
        $actual = $this->help->getHelp('fake');
        $expect = 'No help available.' . PHP_EOL;
        $this->assertSame($expect, $actual);
    }

    public function testGetHelp()
    {
        $this->help->setSummary("A fake comand.");
        $this->help->setOptions($this->options);
        $this->help->setUsage(array(
            "<arg1>",
            "<arg1> <arg2>",
        ));

        $this->help->setDescr(<<<EOT
    A long description of the command. Now is the time for all good men to come
    to the aid of their country. The quick brown fox jumps over the lazy dog.
    Lorem ipsum dolor and all the rest.
EOT
        );

        $actual = $this->help->getHelp('fake');
        $expect = <<<EOT
<<bold>>SUMMARY<<reset>>
    <<bold>>fake<<reset>> -- A fake comand.

<<bold>>USAGE<<reset>>
    <<ul>>fake<<reset>> <arg1>
    <<ul>>fake<<reset>> <arg1> <arg2>

<<bold>>DESCRIPTION<<reset>>
    A long description of the command. Now is the time for all good men to come
    to the aid of their country. The quick brown fox jumps over the lazy dog.
    Lorem ipsum dolor and all the rest.

<<bold>>OPTIONS<<reset>>
    --bar[=<value>]
        A bar option.

    -d [-d [...]]
    --doom [--doom [...]]
        A repeatable flag.

    -z [<value>] [-z [<value>] [...]]
    --zim[=<value>] [--zim[=<value>] [...]]
        A zim option.

    -b <value>
        No description.

    --baz[=<value>] [--baz[=<value>] [...]]
        A baz option.

    -f
    --foo
        A foo option.

    --irk=<value>
        No description.

EOT;
        $this->assertSame($expect, $actual);
    }
}
