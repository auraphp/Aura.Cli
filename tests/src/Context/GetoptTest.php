<?php
namespace Aura\Cli\Context;

class GetoptTest extends \PHPUnit_Framework_TestCase
{
    protected $getopt;
    
    protected function setUp()
    {
        $this->getopt = new Getopt;
    }
    
    public function testSetOptions()
    {
        $options = [
            'foo-bar,f*:', 
            'baz-dib,b::' => 'Description for baz-dib option.',
            'z,zim-gir',
        ];
        
        $this->getopt->setOptions($options);
        $expect = [
            '--foo-bar' => [
                'name'  => '--foo-bar',
                'alias' => '-f',
                'multi' => true,
                'param' => 'required',
                'descr' => null,
            ],
            '--baz-dib' => [
                'name'  => '--baz-dib',
                'alias' => '-b',
                'multi' => false,
                'param' => 'optional',
                'descr' => 'Description for baz-dib option.',
            ],
            '-z' => [
                'name'  => '-z',
                'alias' => '--zim-gir',
                'multi' => false,
                'param' => 'rejected',
                'descr' => null,
            ],
        ];
        
        $actual = $this->getopt->getOptions();
        $this->assertSame($expect, $actual);
        
        // get an aliased option
        $actual = $this->getopt->getOption('--zim-gir');
        $this->assertSame($expect['-z'], $actual);
        
        // get an undefined short flag
        $actual = $this->getopt->getOption('n');
        $expect = [
            'name'  => '-n',
            'alias' => null,
            'multi' => false,
            'param' => 'rejected',
            'descr' => null,
        ];
        $this->assertSame($expect, $actual);
        
        // get an undefined long option
        $actual = $this->getopt->getOption('no-long');
        $expect = [
            'name'  => '--no-long',
            'alias' => null,
            'multi' => false,
            'param' => 'optional',
            'descr' => NULL,
        ];
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_noOptions()
    {
        $result = $this->getopt->parse(['abc', 'def']);
        $this->assertTrue($result);
        
        $expect = ['abc', 'def'];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_longRejected()
    {
        $options = ['foo-bar'];
        $this->getopt->setOptions($options);
        
        $result = $this->getopt->parse(['--foo-bar']);
        $this->assertTrue($result);
        
        $expect = ['--foo-bar' => true];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
        
        $result = $this->getopt->parse(['--foo-bar=baz']);
        $this->assertFalse($result);
        
        $errors = $this->getopt->getErrors();
        $actual = $errors[0];
        $expect = 'Aura\Cli\Exception\OptionParamRejected';
        $this->assertInstanceOf($expect, $actual);
        $expect = "The option '--foo-bar' does not accept a parameter.";
        $this->assertSame($expect, $actual->getMessage());
    }
    
    public function testParse_longRequired()
    {
        $options = ['foo-bar:'];
        $this->getopt->setOptions($options);
        
        $result = $this->getopt->parse(['--foo-bar=baz']);
        $this->assertTrue($result);
        
        $expect = ['--foo-bar' => 'baz'];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
        
        $result = $this->getopt->parse(['--foo-bar']);
        $this->assertFalse($result);
        
        $errors = $this->getopt->getErrors();
        $actual = $errors[0];
        $expect = 'Aura\Cli\Exception\OptionParamRequired';
        $this->assertInstanceOf($expect, $actual);
        $expect = "The option '--foo-bar' requires a parameter.";
        $this->assertSame($expect, $actual->getMessage());
    }
    
    public function testParse_longOptional()
    {
        $options = ['foo-bar::'];
        $this->getopt->setOptions($options);
        
        $result = $this->getopt->parse(['--foo-bar']);
        $this->assertTrue($result);
        
        $expect = ['--foo-bar' => true];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
        
        $result = $this->getopt->parse(['--foo-bar=baz']);
        $this->assertTrue($result);
        
        $expect = ['--foo-bar' => 'baz'];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_longMultiple()
    {
        $options = ['foo-bar*::'];
        $this->getopt->setOptions($options);
        
        $result = $this->getopt->parse([
            '--foo-bar',
            '--foo-bar',
            '--foo-bar=baz',
            '--foo-bar=dib',
            '--foo-bar'
        ]);
        $this->assertTrue($result);
        
        $expect = ['--foo-bar' => [true, true, 'baz', 'dib', true]];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortRejected()
    {
        $options = ['f'];
        $this->getopt->setOptions($options);
        
        $result = $this->getopt->parse(['-f']);
        $this->assertTrue($result);
        
        $expect = ['-f' => true];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
        
        $result = $this->getopt->parse(['-f', 'baz']);
        $this->assertTrue($result);
        
        $expect = ['-f' => true, 'baz'];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortRequired()
    {
        $options = ['f:'];
        $this->getopt->setOptions($options);
        
        $result = $this->getopt->parse(['-f', 'baz']);
        $this->assertTrue($result);
        
        $expect = ['-f' => 'baz'];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
    
        $result = $this->getopt->parse(['-f']);
        $this->assertFalse($result);
        
        $errors = $this->getopt->getErrors();
        $actual = $errors[0];
        $expect = 'Aura\Cli\Exception\OptionParamRequired';
        $this->assertInstanceOf($expect, $actual);
        $expect = "The option '-f' requires a parameter.";
        $this->assertSame($expect, $actual->getMessage());
    }
    
    public function testParse_shortOptional()
    {
        $options = ['f::'];
        $this->getopt->setOptions($options);
        
        $result = $this->getopt->parse(['-f']);
        $this->assertTrue($result);
        
        $expect = ['-f' => true];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
        
        $result = $this->getopt->parse(['-f', 'baz']);
        $this->assertTrue($result);
        
        $expect = ['-f' => 'baz'];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortMultiple()
    {
        $options = ['f*::'];
        $this->getopt->setOptions($options);
        
        $result = $this->getopt->parse(['-f', '-f', '-f', 'baz', '-f', 'dib', '-f']);
        $this->assertTrue($result);
        
        $expect = ['-f' => [true, true, 'baz', 'dib', true]];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortCluster()
    {
        $options = ['f', 'b', 'z'];
        $this->getopt->setOptions($options);
        
        $result = $this->getopt->parse(['-fbz']);
        $this->assertTrue($result);
        
        $expect = [
            '-f' => true,
            '-b' => true,
            '-z' => true,
        ];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortClusterRequired()
    {
        $options = ['f', 'b:', 'z'];
        $this->getopt->setOptions($options);
    
        $result = $this->getopt->parse(['-fbz']);
        $this->assertFalse($result);
        
        $errors = $this->getopt->getErrors();
        $actual = $errors[0];
        $expect = 'Aura\Cli\Exception\OptionParamRequired';
        $this->assertInstanceOf($expect, $actual);
        $expect = "The option '-b' requires a parameter.";
        $this->assertSame($expect, $actual->getMessage());
    }
    
    public function testParseAndGet()
    {
        $this->getopt->setOptions(['foo-bar:', 'b', 'z::']);
        $this->getopt->parse([
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
        ]);
        
        // all values
        $expect = [
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
        ];
        
        // get all values
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
        
        // get one value
        $actual = $this->getopt->get('-z');
        $expect = 'qux';
        $this->assertSame($expect, $actual);
        
        // get alt value
        $actual = $this->getopt->get('--no-such-key', 'DOOM');
        $expect = 'DOOM';
        $this->assertSame($expect, $actual);
    }
}
