<?php
namespace Aura\Cli\Context;

use Aura\Cli\GetoptParser;

class GetoptTest extends \PHPUnit_Framework_TestCase
{
    protected $getopt;
    
    protected function setUp()
    {
        $this->getopt = new Getopt(new GetoptParser);
    }
    
    public function testSetOptions()
    {
        $options = array(
            'foo-bar,f*:', 
            'baz-dib,b::' => 'Description for baz-dib option.',
            'z,zim-gir',
        );
        
        $this->getopt->setOptions($options);
        $expect = array(
            '--foo-bar' => array(
                'name'  => '--foo-bar',
                'alias' => '-f',
                'multi' => true,
                'param' => 'required',
                'descr' => null,
            ),
            '--baz-dib' => array(
                'name'  => '--baz-dib',
                'alias' => '-b',
                'multi' => false,
                'param' => 'optional',
                'descr' => 'Description for baz-dib option.',
            ),
            '-z' => array(
                'name'  => '-z',
                'alias' => '--zim-gir',
                'multi' => false,
                'param' => 'rejected',
                'descr' => null,
            ),
        );
        
        $actual = $this->getopt->getOptions();
        $this->assertSame($expect, $actual);
        
        // get an aliased option
        $actual = $this->getopt->getOption('--zim-gir');
        $this->assertSame($expect['-z'], $actual);
        
        // get an undefined short flag
        $actual = $this->getopt->getOption('n');
        $expect = array(
            'name'  => '-n',
            'alias' => null,
            'multi' => false,
            'param' => 'rejected',
            'descr' => null,
        );
        $this->assertSame($expect, $actual);
        
        // get an undefined long option
        $actual = $this->getopt->getOption('no-long');
        $expect = array(
            'name'  => '--no-long',
            'alias' => null,
            'multi' => false,
            'param' => 'optional',
            'descr' => NULL,
        );
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_noOptions()
    {
        $result = $this->getopt->parse(array('abc', 'def'));
        $this->assertTrue($result);
        
        $expect = array('abc', 'def');
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_longRejected()
    {
        $options = array('foo-bar');
        $this->getopt->setOptions($options);
        
        $result = $this->getopt->parse(array('--foo-bar'));
        $this->assertTrue($result);
        
        $expect = array('--foo-bar' => true);
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
        
        $result = $this->getopt->parse(array('--foo-bar=baz'));
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
        $options = array('foo-bar:');
        $this->getopt->setOptions($options);
        
        $result = $this->getopt->parse(array('--foo-bar=baz'));
        $this->assertTrue($result);
        
        $expect = array('--foo-bar' => 'baz');
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
        
        $result = $this->getopt->parse(array('--foo-bar'));
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
        $options = array('foo-bar::');
        $this->getopt->setOptions($options);
        
        $result = $this->getopt->parse(array('--foo-bar'));
        $this->assertTrue($result);
        
        $expect = array('--foo-bar' => true);
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
        
        $result = $this->getopt->parse(array('--foo-bar=baz'));
        $this->assertTrue($result);
        
        $expect = array('--foo-bar' => 'baz');
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_longMultiple()
    {
        $options = array('foo-bar*::');
        $this->getopt->setOptions($options);
        
        $result = $this->getopt->parse(array(
            '--foo-bar',
            '--foo-bar',
            '--foo-bar=baz',
            '--foo-bar=dib',
            '--foo-bar'
        ));
        $this->assertTrue($result);
        
        $expect = array('--foo-bar' => array(true, true, 'baz', 'dib', true));
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortRejected()
    {
        $options = array('f');
        $this->getopt->setOptions($options);
        
        $result = $this->getopt->parse(array('-f'));
        $this->assertTrue($result);
        
        $expect = array('-f' => true);
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
        
        $result = $this->getopt->parse(array('-f', 'baz'));
        $this->assertTrue($result);
        
        $expect = array('-f' => true, 'baz');
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortRequired()
    {
        $options = array('f:');
        $this->getopt->setOptions($options);
        
        $result = $this->getopt->parse(array('-f', 'baz'));
        $this->assertTrue($result);
        
        $expect = array('-f' => 'baz');
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
    
        $result = $this->getopt->parse(array('-f'));
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
        $options = array('f::');
        $this->getopt->setOptions($options);
        
        $result = $this->getopt->parse(array('-f'));
        $this->assertTrue($result);
        
        $expect = array('-f' => true);
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
        
        $result = $this->getopt->parse(array('-f', 'baz'));
        $this->assertTrue($result);
        
        $expect = array('-f' => 'baz');
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortMultiple()
    {
        $options = array('f*::');
        $this->getopt->setOptions($options);
        
        $result = $this->getopt->parse(array('-f', '-f', '-f', 'baz', '-f', 'dib', '-f'));
        $this->assertTrue($result);
        
        $expect = array('-f' => array(true, true, 'baz', 'dib', true));
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortCluster()
    {
        $options = array('f', 'b', 'z');
        $this->getopt->setOptions($options);
        
        $result = $this->getopt->parse(array('-fbz'));
        $this->assertTrue($result);
        
        $expect = array(
            '-f' => true,
            '-b' => true,
            '-z' => true,
        );
        $actual = $this->getopt->get();
        $this->assertSame($expect, $actual);
    }
    
    public function testParse_shortClusterRequired()
    {
        $options = array('f', 'b:', 'z');
        $this->getopt->setOptions($options);
    
        $result = $this->getopt->parse(array('-fbz'));
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
        $this->getopt->setOptions(array('foo-bar:', 'b', 'z::'));
        $this->getopt->parse(array(
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
