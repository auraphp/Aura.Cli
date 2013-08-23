<?php
namespace Aura\Cli;

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
        
        $this->getopt->setOptDefs($opt_defs);
        $actual = $this->getopt->getOptDefs();
        $this->assertSame($expect, $actual);
        
        $actual = $this->getopt->getOptDef('f');
        $this->assertSame($expect['f'], $actual);
        
        // get an undefined short flag
        $actual = $this->getopt->getOptDef('n');
        $expect = ['name' => 'n', 'param' => 'rejected'];
        $this->assertSame($expect, $actual);
        
        // get an undefined long option
        $actual = $this->getopt->getOptDef('no-long');
        $expect = ['name' => 'no-long', 'param' => 'optional'];
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_noDefs()
    {
        $this->getopt->setInput(['abc', 'def']);
        $result = $this->getopt->parse();
        $this->assertTrue($result);
        
        $expect = ['abc', 'def'];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_longRejected()
    {
        $opt_defs = ['foo-bar'];
        $this->getopt->setOptDefs($opt_defs);
        
        $this->getopt->setInput(['--foo-bar']);
        $result = $this->getopt->parse();
        $this->assertTrue($result);
        
        $expect = ['--foo-bar' => 1];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
        
        $this->getopt->setInput(['--foo-bar=baz']);
        $result = $this->getopt->parse();
        $this->assertFalse($result);
        
        $actual = $this->getopt->getErrors();
        $expect = ["The option '--foo-bar' does not accept a parameter."];
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_longRequired()
    {
        $opt_defs = ['foo-bar:'];
        $this->getopt->setOptDefs($opt_defs);
        
        $this->getopt->setInput(['--foo-bar=baz']);
        $result = $this->getopt->parse();
        $this->assertTrue($result);
        
        $expect = ['--foo-bar' => 'baz'];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
        
        $this->getopt->setInput(['--foo-bar']);
        $result = $this->getopt->parse();
        $this->assertFalse($result);
        
        $actual = $this->getopt->getErrors();
        $expect = ["The option '--foo-bar' requires a parameter."];
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_longOptional()
    {
        $opt_defs = ['foo-bar::'];
        $this->getopt->setOptDefs($opt_defs);
        
        $this->getopt->setInput(['--foo-bar']);
        $result = $this->getopt->parse();
        $this->assertTrue($result);
        
        $expect = ['--foo-bar' => 1];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
        
        $this->getopt->setInput(['--foo-bar=baz']);
        $result = $this->getopt->parse();
        $this->assertTrue($result);
        
        $expect = ['--foo-bar' => 'baz'];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_longMultiple()
    {
        $opt_defs = ['foo-bar::'];
        $this->getopt->setOptDefs($opt_defs);
        
        $this->getopt->setInput([
            '--foo-bar',
            '--foo-bar',
            '--foo-bar=baz',
            '--foo-bar=dib',
            '--foo-bar'
        ]);
        $result = $this->getopt->parse();
        $this->assertTrue($result);
        
        $expect = ['--foo-bar' => [2, 'baz', 'dib', 1]];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortRejected()
    {
        $opt_defs = ['f'];
        $this->getopt->setOptDefs($opt_defs);
        
        $this->getopt->setInput(['-f']);
        $result = $this->getopt->parse();
        $this->assertTrue($result);
        
        $expect = ['-f' => 1];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
        
        $this->getopt->setInput(['-f', 'baz']);
        $result = $this->getopt->parse();
        $this->assertTrue($result);
        
        $expect = ['-f' => 1, 'baz'];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortRequired()
    {
        $opt_defs = ['f:'];
        $this->getopt->setOptDefs($opt_defs);
        
        $this->getopt->setInput(['-f', 'baz']);
        $result = $this->getopt->parse();
        $this->assertTrue($result);
        
        $expect = ['-f' => 'baz'];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);

        $this->getopt->setInput(['-f']);
        $result = $this->getopt->parse();
        $this->assertFalse($result);
        
        $actual = $this->getopt->getErrors();
        $expect = ["The option '-f' requires a parameter."];
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortOptional()
    {
        $opt_defs = ['f::'];
        $this->getopt->setOptDefs($opt_defs);
        
        $this->getopt->setInput(['-f']);
        $result = $this->getopt->parse();
        $this->assertTrue($result);
        
        $expect = ['-f' => 1];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
        
        $this->getopt->setInput(['-f', 'baz']);
        $result = $this->getopt->parse();
        $this->assertTrue($result);
        
        $expect = ['-f' => 'baz'];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortMultiple()
    {
        $opt_defs = ['f::'];
        $this->getopt->setOptDefs($opt_defs);
        
        $this->getopt->setInput(['-f', '-f', '-f', 'baz', '-f', 'dib', '-f']);
        $result = $this->getopt->parse();
        $this->assertTrue($result);
        
        $expect = ['-f' => [2, 'baz', 'dib', 1]];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortCluster()
    {
        $opt_defs = ['f', 'b', 'z'];
        $this->getopt->setOptDefs($opt_defs);
        
        $this->getopt->setInput(['-fbz']);
        $result = $this->getopt->parse();
        $this->assertTrue($result);
        
        $expect = [
            '-f' => 1,
            '-b' => 1,
            '-z' => 1,
        ];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortClusterRequired()
    {
        $opt_defs = ['f', 'b:', 'z'];
        $this->getopt->setOptDefs($opt_defs);

        $this->getopt->setInput(['-fbz']);
        $result = $this->getopt->parse();
        $this->assertFalse($result);
        
        $actual = $this->getopt->getErrors();
        $expect = ["The option '-b' requires a parameter."];
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_namedArgs()
    {
        $expect = ['foo', 'bar', 'baz'];
        $this->getopt->setArgDefs($expect);
        $actual = $this->getopt->getArgDefs();
        $this->assertSame($expect, $actual);
        
        $this->getopt->setInput(['dib', 'qux']);
        $this->getopt->parse();
        $expect = [
            0 => 'dib',
            'foo' => 'dib',
            1 => 'qux',
            'bar' => 'qux',
        ];
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
    }
    
    public function testParseAndGet()
    {
        $this->getopt->setOptDefs(['foo-bar:', 'b', 'z::']);
        $this->getopt->setInput([
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
        $this->getopt->parse();
        
        // all values
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
        
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
        
        // a particular value
        $expect = 'zim';
        $actual = $this->getopt->get('--foo-bar');
        $this->assertSame($expect, $actual);
        
        // an alternative value
        $expect = 'irk';
        $actual = $this->getopt->get('no-such-arg', 'irk');
        $this->assertSame($expect, $actual);
    }
}
