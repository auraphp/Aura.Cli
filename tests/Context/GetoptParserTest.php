<?php
namespace Aura\Cli\Context;

class GetoptParserTest extends \PHPUnit_Framework_TestCase
{
    protected $getopt_parser;

    protected function setUp()
    {
        $this->getopt_parser = new GetoptParser(new OptionFactory);
    }

    public function testSetOptions()
    {
        $options = array(
            '#foo',
            'foo-bar,f*:',
            '#bar' => 'Argument bar required.',
            'baz-dib,b::' => 'Description for baz-dib option.',
            '#baz?' => 'Argument baz optional.',
            'z,zim-gir',
        );

        $this->getopt_parser->setOptions($options);
        $expect = array(
            0 => (object) array(
                'name'  => null,
                'alias' => 'foo',
                'multi' => false,
                'param' => 'argument-required',
                'descr' => null,
            ),
            '--foo-bar' => (object) array(
                'name'  => '--foo-bar',
                'alias' => '-f',
                'multi' => true,
                'param' => 'required',
                'descr' => null,
            ),
            1 => (object) array(
                'name'  => null,
                'alias' => 'bar',
                'multi' => false,
                'param' => 'argument-required',
                'descr' => 'Argument bar required.',
            ),
            '--baz-dib' => (object) array(
                'name'  => '--baz-dib',
                'alias' => '-b',
                'multi' => false,
                'param' => 'optional',
                'descr' => 'Description for baz-dib option.',
            ),
            2 => (object) array(
                'name'  => null,
                'alias' => 'baz',
                'multi' => false,
                'param' => 'argument-optional',
                'descr' => 'Argument baz optional.',
            ),
            '-z' => (object) array(
                'name'  => '-z',
                'alias' => '--zim-gir',
                'multi' => false,
                'param' => 'rejected',
                'descr' => null,
            ),
        );

        $actual = $this->getopt_parser->getOptions();
        $this->assertEquals($expect, $actual);

        // get an aliased option
        $actual = $this->getopt_parser->getOption('--zim-gir');
        $this->assertEquals($expect['-z'], $actual);

        // get an undefined short flag
        $actual = $this->getopt_parser->getOption('n');
        $expect = (object) array(
            'name'  => '-n',
            'alias' => null,
            'multi' => false,
            'param' => 'rejected',
            'descr' => null,
        );
        $this->assertEquals($expect, $actual);

        // get an undefined long option
        $actual = $this->getopt_parser->getOption('--no-long');
        $expect = (object) array(
            'name'  => '--no-long',
            'alias' => null,
            'multi' => false,
            'param' => 'optional',
            'descr' => null,
        );
        $this->assertEquals($expect, $actual);
    }

    public function testParse_noOptions()
    {
        $result = $this->getopt_parser->parseInput(array('abc', 'def'));
        $this->assertTrue($result);

        $expect = array('abc', 'def');
        $actual = $this->getopt_parser->getValues();
        $this->assertSame($expect, $actual);
    }

    public function testParse_longRejected()
    {
        $options = array('foo-bar');
        $this->getopt_parser->setOptions($options);

        $result = $this->getopt_parser->parseInput(array('--foo-bar'));
        $this->assertTrue($result);

        $expect = array('--foo-bar' => true);
        $actual = $this->getopt_parser->getValues();
        $this->assertSame($expect, $actual);

        $result = $this->getopt_parser->parseInput(array('--foo-bar=baz'));
        $this->assertFalse($result);

        $errors = $this->getopt_parser->getErrors();
        $actual = $errors[0];
        $expect = 'Aura\Cli\Exception\OptionParamRejected';
        $this->assertInstanceOf($expect, $actual);
        $expect = "The option '--foo-bar' does not accept a parameter.";
        $this->assertSame($expect, $actual->getMessage());
    }

    public function testParse_longRequired()
    {
        $options = array('foo-bar:');
        $this->getopt_parser->setOptions($options);

        $result = $this->getopt_parser->parseInput(array('--foo-bar=baz'));
        $this->assertTrue($result);

        $expect = array('--foo-bar' => 'baz');
        $actual = $this->getopt_parser->getValues();
        $this->assertSame($expect, $actual);

        $result = $this->getopt_parser->parseInput(array('--foo-bar'));
        $this->assertFalse($result);

        $errors = $this->getopt_parser->getErrors();
        $actual = $errors[0];
        $expect = 'Aura\Cli\Exception\OptionParamRequired';
        $this->assertInstanceOf($expect, $actual);
        $expect = "The option '--foo-bar' requires a parameter.";
        $this->assertSame($expect, $actual->getMessage());
    }

    public function testParse_longOptional()
    {
        $options = array('foo-bar::');
        $this->getopt_parser->setOptions($options);

        $result = $this->getopt_parser->parseInput(array('--foo-bar'));
        $this->assertTrue($result);

        $expect = array('--foo-bar' => true);
        $actual = $this->getopt_parser->getValues();
        $this->assertSame($expect, $actual);

        $result = $this->getopt_parser->parseInput(array('--foo-bar=baz'));
        $this->assertTrue($result);

        $expect = array('--foo-bar' => 'baz');
        $actual = $this->getopt_parser->getValues();
        $this->assertSame($expect, $actual);
    }

    public function testParse_longMultiple()
    {
        $options = array('foo-bar*::');
        $this->getopt_parser->setOptions($options);

        $result = $this->getopt_parser->parseInput(array(
            '--foo-bar',
            '--foo-bar',
            '--foo-bar=baz',
            '--foo-bar=dib',
            '--foo-bar'
        ));
        $this->assertTrue($result);

        $expect = array('--foo-bar' => array(true, true, 'baz', 'dib', true));
        $actual = $this->getopt_parser->getValues();
        $this->assertSame($expect, $actual);
    }

    public function testParse_shortRejected()
    {
        $options = array('f');
        $this->getopt_parser->setOptions($options);

        $result = $this->getopt_parser->parseInput(array('-f'));
        $this->assertTrue($result);

        $expect = array('-f' => true);
        $actual = $this->getopt_parser->getValues();
        $this->assertSame($expect, $actual);

        $result = $this->getopt_parser->parseInput(array('-f', 'baz'));
        $this->assertTrue($result);

        $expect = array('-f' => true, 'baz');
        $actual = $this->getopt_parser->getValues();
        $this->assertSame($expect, $actual);
    }

    public function testParse_shortRequired()
    {
        $options = array('f:');
        $this->getopt_parser->setOptions($options);

        $result = $this->getopt_parser->parseInput(array('-f', 'baz'));
        $this->assertTrue($result);

        $expect = array('-f' => 'baz');
        $actual = $this->getopt_parser->getValues();
        $this->assertSame($expect, $actual);

        $result = $this->getopt_parser->parseInput(array('-f'));
        $this->assertFalse($result);

        $errors = $this->getopt_parser->getErrors();
        $actual = $errors[0];
        $expect = 'Aura\Cli\Exception\OptionParamRequired';
        $this->assertInstanceOf($expect, $actual);
        $expect = "The option '-f' requires a parameter.";
        $this->assertSame($expect, $actual->getMessage());
    }

    public function testParse_shortOptional()
    {
        $options = array('f::');
        $this->getopt_parser->setOptions($options);

        $result = $this->getopt_parser->parseInput(array('-f'));
        $this->assertTrue($result);

        $expect = array('-f' => true);
        $actual = $this->getopt_parser->getValues();
        $this->assertSame($expect, $actual);

        $result = $this->getopt_parser->parseInput(array('-f', 'baz'));
        $this->assertTrue($result);

        $expect = array('-f' => 'baz');
        $actual = $this->getopt_parser->getValues();
        $this->assertSame($expect, $actual);
    }

    public function testParse_shortMultiple()
    {
        $options = array('f*::');
        $this->getopt_parser->setOptions($options);

        $result = $this->getopt_parser->parseInput(array('-f', '-f', '-f', 'baz', '-f', 'dib', '-f'));
        $this->assertTrue($result);

        $expect = array('-f' => array(true, true, 'baz', 'dib', true));
        $actual = $this->getopt_parser->getValues();
        $this->assertSame($expect, $actual);
    }

    public function testParse_shortCluster()
    {
        $options = array('f', 'b', 'z');
        $this->getopt_parser->setOptions($options);

        $result = $this->getopt_parser->parseInput(array('-fbz'));
        $this->assertTrue($result);

        $expect = array(
            '-f' => true,
            '-b' => true,
            '-z' => true,
        );
        $actual = $this->getopt_parser->getValues();
        $this->assertSame($expect, $actual);
    }

    public function testParse_shortClusterRequired()
    {
        $options = array('f', 'b:', 'z');
        $this->getopt_parser->setOptions($options);

        $result = $this->getopt_parser->parseInput(array('-fbz'));
        $this->assertFalse($result);

        $errors = $this->getopt_parser->getErrors();
        $actual = $errors[0];
        $expect = 'Aura\Cli\Exception\OptionParamRequired';
        $this->assertInstanceOf($expect, $actual);
        $expect = "The option '-b' requires a parameter.";
        $this->assertSame($expect, $actual->getMessage());
    }

    public function testParseAndGet()
    {
        $this->getopt_parser->setOptions(array('#foo', 'foo-bar:', '#bar', 'b', '#baz?', 'z::'));
        $this->getopt_parser->parseInput(array(
            'abc',
            '--foo-bar=zim',
            '--undefined=undef',
            'def',
            '-z',
            'qux',
            '-b',
            'gir',
            '--',
            '--after-double-dash=123',
            '-n',
            '456',
            'ghi',
        ));

        // all values
        $expect = array(
            'abc',
            '--foo-bar' => 'zim',
            '--undefined' => 'undef',
            'def',
            '-z' => 'qux',
            '-b' => true,
            'gir',
            '--after-double-dash=123',
            '-n',
            '456',
            'ghi',
        );

        // get all values
        $actual = $this->getopt_parser->getValues();
        $this->assertSame($expect, $actual);
    }

    public function testMultipleWithAlias()
    {
        $options = array('f,foo*::');
        $this->getopt_parser->setOptions($options);

        $result = $this->getopt_parser->parseInput(array('-f', '-f', '-f', 'baz', '-f', 'dib', '-f'));
        $this->assertTrue($result);

        $expect = array(
            '-f' => array(true, true, 'baz', 'dib', true),
            '--foo' => array(true, true, 'baz', 'dib', true),
        );
        $actual = $this->getopt_parser->getValues();
        $this->assertSame($expect, $actual);
    }
}
