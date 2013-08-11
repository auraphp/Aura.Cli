<?php
namespace Aura\Cli\Context;

class GetoptTest extends \PHPUnit_Framework_TestCase
{
    protected $getopt;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->getopt = new Getopt;
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
        
        $this->getopt->setDefs($defs);
        $actual = $this->getopt->getDefs();
        $this->assertSame($expect, $actual);
        
        $actual = $this->getopt->getDef('f');
        $this->assertSame($expect['f'], $actual);
        
        $this->setExpectedException('Aura\Cli\Exception\OptionNotDefined');
        $this->getopt->getDef('no-such-def');
    }
    
    public function testParse_noDefs()
    {
        $this->getopt->parse(['abc', 'def']);
        
        $expect = [];
        $actual = $this->getopt->getOpts();
        $this->assertSame($expect, $actual);
        
        $expect = ['abc', 'def'];
        $actual = $this->getopt->getArgs();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_longRejected()
    {
        $defs = ['foo-bar'];
        $this->getopt->setDefs($defs);
        
        $argv = ['--foo-bar'];
        $this->getopt->parse($argv);
        $expect = ['foo-bar' => true];
        $actual = $this->getopt->getOpts();
        $this->assertSame($expect, $actual);
        
        $this->setExpectedException('Aura\Cli\Exception\OptionParamRejected');
        $argv = ['--foo-bar=baz'];
        $this->getopt->parse($argv);
    }
    
    public function testParse_longRequired()
    {
        $defs = ['foo-bar:'];
        $this->getopt->setDefs($defs);
        
        $argv = ['--foo-bar=baz'];
        $this->getopt->parse($argv);
        $expect = ['foo-bar' => 'baz'];
        $actual = $this->getopt->getOpts();
        $this->assertSame($expect, $actual);
        
        $this->setExpectedException('Aura\Cli\Exception\OptionParamRequired');
        $argv = ['--foo-bar'];
        $this->getopt->parse($argv);
    }
    
    public function testParse_longOptional()
    {
        $defs = ['foo-bar::'];
        $this->getopt->setDefs($defs);
        
        $argv = ['--foo-bar'];
        $this->getopt->parse($argv);
        $expect = ['foo-bar' => true];
        $actual = $this->getopt->getOpts();
        $this->assertSame($expect, $actual);
        
        $argv = ['--foo-bar=baz'];
        $this->getopt->parse($argv);
        $expect = ['foo-bar' => 'baz'];
        $actual = $this->getopt->getOpts();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_longMultiple()
    {
        $defs = ['foo-bar::'];
        $this->getopt->setDefs($defs);
        
        $argv = ['--foo-bar', '--foo-bar=baz', '--foo-bar=dib'];
        $this->getopt->parse($argv);
        $expect = ['foo-bar' => [true, 'baz', 'dib']];
        $actual = $this->getopt->getOpts();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortRejected()
    {
        $defs = ['f'];
        $this->getopt->setDefs($defs);
        
        $argv = ['-f'];
        $this->getopt->parse($argv);
        $expect = ['f' => true];
        $actual = $this->getopt->getOpts();
        $this->assertSame($expect, $actual);
        
        $argv = ['-f', 'baz'];
        $this->getopt->parse($argv);
        $expect = ['f' => true];
        $actual = $this->getopt->getOpts();
        $this->assertSame($expect, $actual);
        $expect = ['baz'];
        $actual = $this->getopt->getArgs();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortRequired()
    {
        $defs = ['f:'];
        $this->getopt->setDefs($defs);
        
        $argv = ['-f', 'baz'];
        $this->getopt->parse($argv);
        $expect = ['f' => 'baz'];
        $actual = $this->getopt->getOpts();
        $this->assertSame($expect, $actual);
        
        $this->setExpectedException('Aura\Cli\Exception\OptionParamRequired');
        $argv = ['-f'];
        $this->getopt->parse($argv);
    }
    
    public function testParse_shortOptional()
    {
        $defs = ['f::'];
        $this->getopt->setDefs($defs);
        
        $argv = ['-f'];
        $this->getopt->parse($argv);
        $expect = ['f' => true];
        $actual = $this->getopt->getOpts();
        $this->assertSame($expect, $actual);
        
        $argv = ['-f', 'baz'];
        $this->getopt->parse($argv);
        $expect = ['f' => 'baz'];
        $actual = $this->getopt->getOpts();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortMultiple()
    {
        $defs = ['f::'];
        $this->getopt->setDefs($defs);
        
        $argv = ['-f', '-f', 'baz', '-f', 'dib'];
        $this->getopt->parse($argv);
        $expect = ['f' => [true, 'baz', 'dib']];
        $actual = $this->getopt->getOpts();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortCluster()
    {
        $defs = ['f', 'b', 'z'];
        $this->getopt->setDefs($defs);
        
        $argv = ['-fbz'];
        $this->getopt->parse($argv);
        
        $expect = [
            'f' => true,
            'b' => true,
            'z' => true,
        ];
        $actual = $this->getopt->getOpts();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortClusterRequired()
    {
        $defs = ['f', 'b:', 'z'];
        $this->getopt->setDefs($defs);
        $this->setExpectedException('Aura\Cli\Exception\OptionParamRequired');
        $this->getopt->parse(['-fbz']);
    }
    
    public function testPrase_namedArgs()
    {
        $expect = ['foo', 'bar', 'baz'];
        $this->getopt->setArgNames($expect);
        $actual = $this->getopt->getArgNames();
        $this->assertSame($expect, $actual);
        
        $this->getopt->parse(['dib', 'qux']);
        $expect = [
            0 => 'dib',
            1 => 'qux',
            'foo' => 'dib',
            'bar' => 'qux',
        ];
        $actual = $this->getopt->getArgs();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse()
    {
        $this->getopt->setDefs(['foo-bar:', 'b', 'z::']);
        $this->getopt->parse([
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
        $actual = $this->getopt->getOpts();
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
        $actual = $this->getopt->getArgs();
        $this->assertSame($expect, $actual);
    }
    
    public function testStrict()
    {
        $this->getopt->setStrict(false);
        $this->assertFalse($this->getopt->getStrict());
        
        // short flag in non-strict mode
        $expect = ['name' => 'u', 'param' => 'rejected'];
        $actual = $this->getopt->getDef('u');
        $this->assertSame($expect, $actual);
        
        // long option in non-strict mode
        $expect = ['name' => 'undef', 'param' => 'optional'];
        $actual = $this->getopt->getDef('undef');
        $this->assertSame($expect, $actual);
    }
}
