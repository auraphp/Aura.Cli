<?php
namespace Aura\Cli;

use Aura\Cli\Context\OptionFactory;

class HelpTest extends \PHPUnit_Framework_TestCase
{
    protected $help;

    protected $options = array(
        '#foo',
        '#bar' => 'Arg bar is required.',
        '#baz?' => 'Arg baz is optional.',
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
        $this->help = new Help(new OptionFactory);
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

<<bold>>ARGUMENTS<<reset>>
    <foo>
        No description.

    <bar>
        Arg bar is required.

    <baz>
        Arg baz is optional.

<<bold>>OPTIONS<<reset>>
    -z [<value>] [-z [<value>] [...]]
    --zim[=<value>] [--zim[=<value>] [...]]
        A zim option.

    -f
    --foo
        A foo option.

    -b <value>
        No description.

    --irk=<value>
        No description.

    -d [-d [...]]
    --doom [--doom [...]]
        A repeatable flag.

    --bar[=<value>]
        A bar option.

    --baz[=<value>] [--baz[=<value>] [...]]
        A baz option.

EOT;
        $this->assertSame($expect, $actual);
    }

    public function testGetHelpWithoutUsage()
    {
        $this->help->setSummary("A fake comand.");
        $this->help->setOptions($this->options);

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
    <<ul>>fake<<reset>> <foo> <bar> [<baz>]

<<bold>>DESCRIPTION<<reset>>
    A long description of the command. Now is the time for all good men to come
    to the aid of their country. The quick brown fox jumps over the lazy dog.
    Lorem ipsum dolor and all the rest.

<<bold>>ARGUMENTS<<reset>>
    <foo>
        No description.

    <bar>
        Arg bar is required.

    <baz>
        Arg baz is optional.

<<bold>>OPTIONS<<reset>>
    -z [<value>] [-z [<value>] [...]]
    --zim[=<value>] [--zim[=<value>] [...]]
        A zim option.

    -f
    --foo
        A foo option.

    -b <value>
        No description.

    --irk=<value>
        No description.

    -d [-d [...]]
    --doom [--doom [...]]
        A repeatable flag.

    --bar[=<value>]
        A bar option.

    --baz[=<value>] [--baz[=<value>] [...]]
        A baz option.

EOT;
        $this->assertSame($expect, $actual);
    }
}
