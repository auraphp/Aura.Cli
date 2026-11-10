<?php
namespace Aura\Cli;

use Aura\Cli\Context\OptionFactory;

class HelpTest extends \PHPUnit\Framework\TestCase
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

    protected function setUp(): void
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

    public function testAddAndGetSections()
    {
        $this->assertSame(array(), $this->help->getSections());

        $this->help->addSection('EXAMPLES', 'fake build');
        $this->help->addSection('SEE ALSO', array('other(1)'));

        $expect = array(
            'EXAMPLES' => 'fake build',
            'SEE ALSO' => array('other(1)'),
        );
        $this->assertSame($expect, $this->help->getSections());
    }

    public function testGetHelpWithSections()
    {
        $this->help->setSummary("A fake comand.");
        $this->help->setUsage('<target>');
        $this->help->setOptions(array(
            'v,verbose' => "Verbose output.",
        ));

        // array text: one line per element
        $this->help->addSection('EXAMPLES', array(
            'fake build -v',
            'fake build --verbose',
        ));

        // string text: split into lines; relative indentation is kept
        $this->help->addSection('SEE ALSO', "other(1)\n\n  another(1)\n");

        $actual = $this->help->getHelp('fake');
        $expect = <<<EOT
<<bold>>SUMMARY<<reset>>
    <<bold>>fake<<reset>> -- A fake comand.

<<bold>>USAGE<<reset>>
    <<ul>>fake<<reset>> <target>

<<bold>>OPTIONS<<reset>>
    -v
    --verbose
        Verbose output.

<<bold>>EXAMPLES<<reset>>
    fake build -v
    fake build --verbose

<<bold>>SEE ALSO<<reset>>
    other(1)

      another(1)

EOT;
        $this->assertSame($expect, $actual);
    }

    public function testAddSectionReplacesSameTitle()
    {
        $this->help->addSection('EXAMPLES', 'first');
        $this->help->addSection('NOTES', 'a note');
        $this->help->addSection('EXAMPLES', 'second');

        // replaced in place, so the original order is kept
        $expect = array(
            'EXAMPLES' => 'second',
            'NOTES' => 'a note',
        );
        $this->assertSame($expect, $this->help->getSections());

        $actual = $this->help->getHelp('fake');
        $expect = <<<EOT
<<bold>>EXAMPLES<<reset>>
    second

<<bold>>NOTES<<reset>>
    a note

EOT;
        $this->assertSame($expect, $actual);
    }

    public function testGetHelpWithOnlySections()
    {
        $this->help->addSection('EXAMPLES', 'fake build');

        $actual = $this->help->getHelp('fake');
        $expect = <<<EOT
<<bold>>EXAMPLES<<reset>>
    fake build

EOT;
        $this->assertSame($expect, $actual);
    }
}
