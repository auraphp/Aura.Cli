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
        $opt_defs = [
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
        
        $this->optarg->setOptDefs($opt_defs);
        $actual = $this->optarg->getOptDefs();
        $this->assertSame($expect, $actual);
        
        $actual = $this->optarg->getOptDef('f');
        $this->assertSame($expect['f'], $actual);
        
        // get an undefined short flag
        $actual = $this->optarg->getOptDef('n');
        $expect = ['name' => 'n', 'param' => 'rejected'];
        $this->assertSame($expect, $actual);
        
        // get an undefined long option
        $actual = $this->optarg->getOptDef('no-long');
        $expect = ['name' => 'no-long', 'param' => 'optional'];
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_noDefs()
    {
        $argv = ['abc', 'def'];
        $result = $this->optarg->parse($argv);
        $this->assertTrue($result);
        
        $expect = ['abc', 'def'];
        $actual = $this->optarg->getValues();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_longRejected()
    {
        $opt_defs = ['foo-bar'];
        $this->optarg->setOptDefs($opt_defs);
        
        $argv = ['--foo-bar'];
        $result = $this->optarg->parse($argv);
        $this->assertTrue($result);
        
        $expect = ['--foo-bar' => 1];
        $actual = $this->optarg->getValues();
        $this->assertSame($expect, $actual);
        
        $argv = ['--foo-bar=baz'];
        $result = $this->optarg->parse($argv);
        $this->assertFalse($result);
        
        $actual = $this->optarg->getErrors();
        $expect = ["The option '--foo-bar' does not accept a parameter."];
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_longRequired()
    {
        $opt_defs = ['foo-bar:'];
        $this->optarg->setOptDefs($opt_defs);
        
        $argv = ['--foo-bar=baz'];
        $result = $this->optarg->parse($argv);
        $this->assertTrue($result);
        
        $expect = ['--foo-bar' => 'baz'];
        $actual = $this->optarg->getValues();
        $this->assertSame($expect, $actual);
        
        $argv = ['--foo-bar'];
        $result = $this->optarg->parse($argv);
        $this->assertFalse($result);
        
        $actual = $this->optarg->getErrors();
        $expect = ["The option '--foo-bar' requires a parameter."];
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_longOptional()
    {
        $opt_defs = ['foo-bar::'];
        $this->optarg->setOptDefs($opt_defs);
        
        $argv = ['--foo-bar'];
        $result = $this->optarg->parse($argv);
        $this->assertTrue($result);
        
        $expect = ['--foo-bar' => 1];
        $actual = $this->optarg->getValues();
        $this->assertSame($expect, $actual);
        
        $argv = ['--foo-bar=baz'];
        $result = $this->optarg->parse($argv);
        $this->assertTrue($result);
        
        $expect = ['--foo-bar' => 'baz'];
        $actual = $this->optarg->getValues();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_longMultiple()
    {
        $opt_defs = ['foo-bar::'];
        $this->optarg->setOptDefs($opt_defs);
        
        $argv = ['--foo-bar', '--foo-bar', '--foo-bar=baz', '--foo-bar=dib', '--foo-bar'];
        $result = $this->optarg->parse($argv);
        $this->assertTrue($result);
        
        $expect = ['--foo-bar' => [2, 'baz', 'dib', 1]];
        $actual = $this->optarg->getValues();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortRejected()
    {
        $opt_defs = ['f'];
        $this->optarg->setOptDefs($opt_defs);
        
        $argv = ['-f'];
        $result = $this->optarg->parse($argv);
        $this->assertTrue($result);
        
        $expect = ['-f' => 1];
        $actual = $this->optarg->getValues();
        $this->assertSame($expect, $actual);
        
        $argv = ['-f', 'baz'];
        $result = $this->optarg->parse($argv);
        $this->assertTrue($result);
        
        $expect = ['-f' => 1, 'baz'];
        $actual = $this->optarg->getValues();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortRequired()
    {
        $opt_defs = ['f:'];
        $this->optarg->setOptDefs($opt_defs);
        
        $argv = ['-f', 'baz'];
        $result = $this->optarg->parse($argv);
        $this->assertTrue($result);
        
        $expect = ['-f' => 'baz'];
        $actual = $this->optarg->getValues();
        $this->assertSame($expect, $actual);

        $argv = ['-f'];
        $result = $this->optarg->parse($argv);
        $this->assertFalse($result);
        
        $actual = $this->optarg->getErrors();
        $expect = ["The option '-f' requires a parameter."];
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortOptional()
    {
        $opt_defs = ['f::'];
        $this->optarg->setOptDefs($opt_defs);
        
        $argv = ['-f'];
        $result = $this->optarg->parse($argv);
        $this->assertTrue($result);
        
        $expect = ['-f' => 1];
        $actual = $this->optarg->getValues();
        $this->assertSame($expect, $actual);
        
        $argv = ['-f', 'baz'];
        $result = $this->optarg->parse($argv);
        $this->assertTrue($result);
        
        $expect = ['-f' => 'baz'];
        $actual = $this->optarg->getValues();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortMultiple()
    {
        $opt_defs = ['f::'];
        $this->optarg->setOptDefs($opt_defs);
        
        $argv = ['-f', '-f', '-f', 'baz', '-f', 'dib', '-f'];
        $result = $this->optarg->parse($argv);
        $this->assertTrue($result);
        
        $expect = ['-f' => [2, 'baz', 'dib', 1]];
        $actual = $this->optarg->getValues();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortCluster()
    {
        $opt_defs = ['f', 'b', 'z'];
        $this->optarg->setOptDefs($opt_defs);
        
        $argv = ['-fbz'];
        $result = $this->optarg->parse($argv);
        $this->assertTrue($result);
        
        $expect = [
            '-f' => 1,
            '-b' => 1,
            '-z' => 1,
        ];
        $actual = $this->optarg->getValues();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortClusterRequired()
    {
        $opt_defs = ['f', 'b:', 'z'];
        $this->optarg->setOptDefs($opt_defs);

        $argv = ['-fbz'];
        $result = $this->optarg->parse($argv);
        $this->assertFalse($result);
        
        $actual = $this->optarg->getErrors();
        $expect = ["The option '-b' requires a parameter."];
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_namedArgs()
    {
        $expect = ['foo', 'bar', 'baz'];
        $this->optarg->setArgDefs($expect);
        $actual = $this->optarg->getArgDefs();
        $this->assertSame($expect, $actual);
        
        $this->optarg->parse(['dib', 'qux']);
        $expect = [
            0 => 'dib',
            'foo' => 'dib',
            1 => 'qux',
            'bar' => 'qux',
        ];
        $actual = $this->optarg->getValues();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse()
    {
        $this->optarg->setOptDefs(['foo-bar:', 'b', 'z::']);
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
        
        // check values
        $expect = [
            '--foo-bar' => 'zim',
            '-z' => 'qux',
            '-b' => 1,
            'abc',
            'def',
            'gir',
            '--no-such-option=123',
            '-n',
            '456',
            'ghi',
        ];
        
        $actual = $this->optarg->getValues();
        $this->assertSame($expect, $actual);
    }
}
