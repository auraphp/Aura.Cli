<?php
namespace Aura\Cli;

class OptargTest extends \PHPUnit_Framework_TestCase
{
    protected $optarg;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->optarg = new Optarg;
    }
    
    public function testSetAndGetDefs()
    {
        $defs = [
            'foo-bar', 
            'f:' => 'foo-bar',
            'baz-dib::',
            'b' => 'baz-dib',
        ];
        
        $expect = [
            'foo-bar' => [
                'name' => 'foo-bar',
                'param' => 'rejected',
            ],
            'f' => [
                'name' => 'foo-bar',
                'param' => 'required',
            ],
            'baz-dib' => [
                'name' => 'baz-dib',
                'param' => 'optional',
            ],
            'b' => [
                'name' => 'baz-dib',
                'param' => 'rejected',
            ],
        ];
        
        $this->optarg->setDefs($defs);
        $actual = $this->optarg->getDefs();
        $this->assertSame($expect, $actual);
        
        $actual = $this->optarg->getDef('f');
        $this->assertSame($expect['f'], $actual);
        
        $this->setExpectedException('Aura\Cli\Exception\OptionNotDefined');
        $this->optarg->getDef('no-such-def');
    }
    
    public function testParse_noDefs()
    {
        $this->optarg->parse(['abc', 'def']);
        
        $expect = [];
        $actual = $this->optarg->getOpts();
        $this->assertSame($expect, $actual);
        
        $expect = ['abc', 'def'];
        $actual = $this->optarg->getArgs();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_longRejected()
    {
        $defs = ['foo-bar'];
        $this->optarg->setDefs($defs);
        
        $argv = ['--foo-bar'];
        $this->optarg->parse($argv);
        $expect = ['foo-bar' => true];
        $actual = $this->optarg->getOpts();
        $this->assertSame($expect, $actual);
        
        $this->setExpectedException('Aura\Cli\Exception\OptionParamRejected');
        $argv = ['--foo-bar=baz'];
        $this->optarg->parse($argv);
    }
    
    public function testParse_longRequired()
    {
        $defs = ['foo-bar:'];
        $this->optarg->setDefs($defs);
        
        $argv = ['--foo-bar=baz'];
        $this->optarg->parse($argv);
        $expect = ['foo-bar' => 'baz'];
        $actual = $this->optarg->getOpts();
        $this->assertSame($expect, $actual);
        
        $this->setExpectedException('Aura\Cli\Exception\OptionParamRequired');
        $argv = ['--foo-bar'];
        $this->optarg->parse($argv);
    }
    
    public function testParse_longOptional()
    {
        $defs = ['foo-bar::'];
        $this->optarg->setDefs($defs);
        
        $argv = ['--foo-bar'];
        $this->optarg->parse($argv);
        $expect = ['foo-bar' => true];
        $actual = $this->optarg->getOpts();
        $this->assertSame($expect, $actual);
        
        $argv = ['--foo-bar=baz'];
        $this->optarg->parse($argv);
        $expect = ['foo-bar' => 'baz'];
        $actual = $this->optarg->getOpts();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_longMultiple()
    {
        $defs = ['foo-bar::'];
        $this->optarg->setDefs($defs);
        
        $argv = ['--foo-bar', '--foo-bar=baz', '--foo-bar=dib'];
        $this->optarg->parse($argv);
        $expect = ['foo-bar' => [true, 'baz', 'dib']];
        $actual = $this->optarg->getOpts();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortRejected()
    {
        $defs = ['f'];
        $this->optarg->setDefs($defs);
        
        $argv = ['-f'];
        $this->optarg->parse($argv);
        $expect = ['f' => true];
        $actual = $this->optarg->getOpts();
        $this->assertSame($expect, $actual);
        
        $argv = ['-f', 'baz'];
        $this->optarg->parse($argv);
        $expect = ['f' => true];
        $actual = $this->optarg->getOpts();
        $this->assertSame($expect, $actual);
        $expect = ['baz'];
        $actual = $this->optarg->getArgs();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortRequired()
    {
        $defs = ['f:'];
        $this->optarg->setDefs($defs);
        
        $argv = ['-f', 'baz'];
        $this->optarg->parse($argv);
        $expect = ['f' => 'baz'];
        $actual = $this->optarg->getOpts();
        $this->assertSame($expect, $actual);
        
        $this->setExpectedException('Aura\Cli\Exception\OptionParamRequired');
        $argv = ['-f'];
        $this->optarg->parse($argv);
    }
    
    public function testParse_shortOptional()
    {
        $defs = ['f::'];
        $this->optarg->setDefs($defs);
        
        $argv = ['-f'];
        $this->optarg->parse($argv);
        $expect = ['f' => true];
        $actual = $this->optarg->getOpts();
        $this->assertSame($expect, $actual);
        
        $argv = ['-f', 'baz'];
        $this->optarg->parse($argv);
        $expect = ['f' => 'baz'];
        $actual = $this->optarg->getOpts();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortMultiple()
    {
        $defs = ['f::'];
        $this->optarg->setDefs($defs);
        
        $argv = ['-f', '-f', 'baz', '-f', 'dib'];
        $this->optarg->parse($argv);
        $expect = ['f' => [true, 'baz', 'dib']];
        $actual = $this->optarg->getOpts();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortCluster()
    {
        $defs = ['f', 'b', 'z'];
        $this->optarg->setDefs($defs);
        
        $argv = ['-fbz'];
        $this->optarg->parse($argv);
        
        $expect = [
            'f' => true,
            'b' => true,
            'z' => true,
        ];
        $actual = $this->optarg->getOpts();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortClusterRequired()
    {
        $defs = ['f', 'b:', 'z'];
        $this->optarg->setDefs($defs);
        $this->setExpectedException('Aura\Cli\Exception\OptionParamRequired');
        $this->optarg->parse(['-fbz']);
    }
    
    public function testPrase_namedArgs()
    {
        $expect = ['foo', 'bar', 'baz'];
        $this->optarg->setArgNames($expect);
        $actual = $this->optarg->getArgNames();
        $this->assertSame($expect, $actual);
        
        $this->optarg->parse(['dib', 'qux']);
        $expect = [
            0 => 'dib',
            1 => 'qux',
            'foo' => 'dib',
            'bar' => 'qux',
        ];
        $actual = $this->optarg->getArgs();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse()
    {
        $this->optarg->setDefs(['foo-bar:', 'b', 'z::']);
        $this->optarg->parse([
            'abc',
            '--foo-bar=zim',
            'def',
            '-z',
            'qux',
            '-b',
            'gir',
            '--',
            '--no-such-option=123',
            '-n',
            '456',
            'ghi',
        ]);
        
        // check opts
        $expect = [
            'foo-bar' => 'zim',
            'z' => 'qux',
            'b' => true,
        ];
        $actual = $this->optarg->getOpts();
        $this->assertSame($expect, $actual);
        
        // check args
        $expect = [
            'abc',
            'def',
            'gir',
            '--no-such-option=123',
            '-n',
            '456',
            'ghi',
        ];
        $actual = $this->optarg->getArgs();
        $this->assertSame($expect, $actual);
    }
    
    public function testStrict()
    {
        $this->optarg->setStrict(false);
        $this->assertFalse($this->optarg->getStrict());
        
        // short flag in non-strict mode
        $expect = ['name' => 'u', 'param' => 'rejected'];
        $actual = $this->optarg->getDef('u');
        $this->assertSame($expect, $actual);
        
        // long option in non-strict mode
        $expect = ['name' => 'undef', 'param' => 'optional'];
        $actual = $this->optarg->getDef('undef');
        $this->assertSame($expect, $actual);
    }
}
