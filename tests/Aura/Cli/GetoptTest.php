<?php
namespace Aura\Cli;

/**
 * Test class for Getopt.
 */
class GetoptTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Getopt
     */
    protected $getopt;
    
    protected $option_factory;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->option_factory = new OptionFactory;
        $this->getopt = new Getopt($this->option_factory);
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }
    
    public function testInit($strict = Getopt::STRICT)
    {
        $opts = [
            'foo_bar' => [
                'long' => 'foo-bar',
                'short' => 'f',
            ],
            'baz_dib' => [
                'long' => 'baz-dib',
                'short' => 'b',
            ],
        ];
        
        $this->getopt->init($opts, $strict);
        
        $expect = 2;
        $actual = count($this->getopt->getOptions());
        $this->assertEquals($expect, $actual);
    }
    
    /**
     * @expectedException Aura\Cli\Exception
     */
    public function testInit_alreadyInitialized()
    {
        $opts = [
            'foo_bar' => [
                'long' => 'foo-bar',
                'short' => 'f',
            ],
            'baz_dib' => [
                'long' => 'baz-dib',
                'short' => 'b',
            ],
        ];
        
        $this->getopt->init($opts);
        $this->getopt->init($opts);
    }
    
    /**
     * @expectedException \UnexpectedValueException
     */
    public function testInit_unexpected()
    {
        $opts = [
            'foo_bar' => true,
            'baz_dib' => true,
        ];
        
        $this->getopt->init($opts);
    }
    
    public function testGetOptions()
    {
        $this->testInit();
        
        $expect = [];
        $expect['foo_bar'] = $this->option_factory->newInstance([
            'name' => 'foo_bar',
            'long' => 'foo-bar',
            'short' => 'f',
        ]);
        
        $expect['baz_dib'] = $this->option_factory->newInstance([
            'name' => 'baz_dib',
            'long' => 'baz-dib',
            'short' => 'b',
        ]);
        
        $actual = $this->getopt->getOptions();
        
        $this->assertEquals($expect['foo_bar'], $actual['foo_bar']);
        $this->assertEquals($expect['baz_dib'], $actual['baz_dib']);
    }
    
    public function testGetOption()
    {
        $this->testInit();
        
        $expect = $this->option_factory->newInstance([
            'name' => 'foo_bar',
            'long' => 'foo-bar',
            'short' => 'f',
        ]);
        
        $actual = $this->getopt->getOption('foo_bar');
        
        $this->assertEquals($expect, $actual);
    }
    
    /**
     * @expectedException Aura\Cli\Exception\OptionNotDefined
     */
    public function testGetOption_optionNotDefined()
    {
        $this->testInit();
        $actual = $this->getopt->getOption('no_such_option');
    }
    
    public function testGetOption_optionNotDefined_nonStrict()
    {
        $this->testInit(Getopt::NON_STRICT);
        $actual = $this->getopt->getOption('no_such_option');
        $this->assertNull($actual);
    }
    
    public function testGetLongOption()
    {
        $this->testInit();
        
        $expect = $this->option_factory->newInstance([
            'name' => 'foo_bar',
            'long' => 'foo-bar',
            'short' => 'f',
        ]);
        
        $actual = $this->getopt->getLongOption('foo-bar');
        
        $this->assertEquals($expect, $actual);
    }

    /**
     * @expectedException Aura\Cli\Exception\OptionNotDefined
     */
    public function testGetLongOption_optionNotDefined()
    {
        $this->testInit();
        $actual = $this->getopt->getLongOption('no_such_option');
    }
    
    public function testGetLongOption_optionNotDefined_nonStrict()
    {
        $this->testInit(Getopt::NON_STRICT);
        $actual = $this->getopt->getLongOption('no_such_option');
        $this->assertNull($actual);
    }
    
    public function testGetShortOption()
    {
        $this->testInit();
        
        $expect = $this->option_factory->newInstance([
            'name' => 'foo_bar',
            'long' => 'foo-bar',
            'short' => 'f',
        ]);
        
        $actual = $this->getopt->getShortOption('f');
        
        $this->assertEquals($expect, $actual);
    }

    /**
     * @expectedException Aura\Cli\Exception\OptionNotDefined
     */
    public function testGetShortOption_optionNotDefined()
    {
        $this->testInit();
        $actual = $this->getopt->getShortOption('z');
    }
    
    public function testGetShortOption_optionNotDefined_nonStrict()
    {
        $this->testInit(Getopt::NON_STRICT);
        $actual = $this->getopt->getShortOption('z');
        $this->assertNull($actual);
    }
    
    public function testLoad_noOptions()
    {
        $this->testInit();
        
        $this->getopt->load([
            'abc',
            'def',
        ]);
        
        // check options
        $expect = [
            'foo_bar' => null,
            'baz_dib' => null,
        ];
        
        $actual = $this->getopt->getOptionValues();
        $this->assertSame($expect, $actual);
        
        // check params
        $expect = [
            'abc',
            'def',
        ];
        $actual = $this->getopt->getParams();
        $this->assertSame($expect, $actual);
    }
    
    public function testLoad_longOptions()
    {
        $this->testInit();
        
        $this->getopt->load([
            'abc',
            '--foo-bar=zim',
            'def',
            '--baz-dib=gir',
        ]);
        
        // check options
        $expect = [
            'foo_bar' => 'zim',
            'baz_dib' => 'gir',
        ];
        
        $actual = $this->getopt->getOptionValues();
        $this->assertSame($expect, $actual);
        
        // check single option values
        $expect = 'zim';
        $actual = $this->getopt->getOptionValue('foo_bar');
        $this->assertSame($expect, $actual);
        
        $expect = 'gir';
        $actual = $this->getopt->getOptionValue('baz_dib');
        $this->assertSame($expect, $actual);
        
        // check params
        $expect = [
            'abc',
            'def',
        ];
        $actual = $this->getopt->getParams();
        $this->assertSame($expect, $actual);
    }
    
    public function testLoad_longOptions_nonStrict()
    {
        $this->testInit(Getopt::NON_STRICT);
        
        $this->getopt->load([
            'abc',
            '--no-such-option=zim',
        ]);
        
        // check single option values
        $actual = $this->getopt->getOptionValue('no_such_option');
        $this->assertNull($actual);
    }
    
    public function testLoad_shortOptions()
    {
        $this->testInit();
        
        $this->getopt->load([
            'abc',
            '-f',
            'zim',
            'def',
            '-b',
            'gir',
        ]);
        
        // check options
        $expect = [
            'foo_bar' => 'zim',
            'baz_dib' => 'gir',
        ];
        $actual = $this->getopt->getOptionValues();
        $this->assertSame($expect, $actual);
        
        // check single option values
        $expect = 'zim';
        $actual = $this->getopt->getOptionValue('foo_bar');
        $this->assertSame($expect, $actual);
        
        $expect = 'gir';
        $actual = $this->getopt->getOptionValue('baz_dib');
        $this->assertSame($expect, $actual);
        
        // check params
        $expect = [
            'abc',
            'def',
        ];
        $actual = $this->getopt->getParams();
        $this->assertSame($expect, $actual);
    }
    
    public function testLoad_shortOptions_nonStrict()
    {
        $this->testInit(Getopt::NON_STRICT);
        
        $this->getopt->load([
            'abc',
            '-z',
        ]);
        
        // check single option values
        $actual = $this->getopt->getOptionValue('no_such_option');
        $this->assertNull($actual);
    }
    
    public function testLoad_mixedOptionsAndParams()
    {
        $this->testInit();
        
        $this->getopt->load([
            'abc',
            '--foo-bar=zim',
            'def',
            '-b',
            'gir',
            '--',
            '--no-such-option=123',
            '-n',
            '456',
            'ghi',
        ]);
        
        // check options
        $expect = [
            'foo_bar' => 'zim',
            'baz_dib' => 'gir',
        ];
        $actual = $this->getopt->getOptionValues();
        $this->assertSame($expect, $actual);
        
        // check single option values
        $expect = 'zim';
        $actual = $this->getopt->getOptionValue('foo_bar');
        $this->assertSame($expect, $actual);
        
        $expect = 'gir';
        $actual = $this->getopt->getOptionValue('baz_dib');
        $this->assertSame($expect, $actual);
        
        // check params
        $expect = [
            'abc',
            'def',
            '--no-such-option=123',
            '-n',
            '456',
            'ghi',
        ];
        $actual = $this->getopt->getParams();
        $this->assertSame($expect, $actual);
    }
    
    public function testLoad_optionalParamsAsFlags()
    {
        $this->testInit();
        
        $this->getopt->load([
            'abc',
            '--foo-bar',
            'def',
            '-b',
        ]);
        
        // check options
        $expect = [
            'foo_bar' => true,
            'baz_dib' => true,
        ];
        $actual = $this->getopt->getOptionValues();
        $this->assertSame($expect, $actual);
        
        // check single option values
        $actual = $this->getopt->getOptionValue('foo_bar');
        $this->assertTrue($actual);
        
        $actual = $this->getopt->getOptionValue('baz_dib');
        $this->assertTrue($actual);
        
        // check params
        $expect = [
            'abc',
            'def',
        ];
        $actual = $this->getopt->getParams();
        $this->assertSame($expect, $actual);
    }
    
    /**
     * @expectedException Aura\Cli\Exception\OptionParamRequired
     */
    public function testLoad_longOptionParamRequired()
    {
        $opts = [
            'foo_bar' => [
                'long' => 'foo-bar',
                'short' => 'f',
                'param' => Option::PARAM_REQUIRED,
            ],
        ];
        
        $this->getopt->init($opts);
        
        $this->getopt->load([
            'abc',
            '--foo-bar', // no param, should fail
            'def',
        ]);
    }
    
    /**
     * @expectedException Aura\Cli\Exception\OptionParamRejected
     */
    public function testLoad_longOptionParamRejected()
    {
        $opts = [
            'foo_bar' => [
                'long' => 'foo-bar',
                'short' => 'f',
                'param' => Option::PARAM_REJECTED,
            ],
        ];
        
        $this->getopt->init($opts);
        
        $this->getopt->load([
            'abc',
            '--foo-bar=zim', // has param, should fail
            'def',
        ]);
    }
    
    /**
     * @expectedException Aura\Cli\Exception\OptionParamRequired
     */
    public function testLoad_shortOptionParamRequired()
    {
        $opts = [
            'foo_bar' => [
                'long' => 'foo-bar',
                'short' => 'f',
                'param' => Option::PARAM_REQUIRED,
            ],
        ];
        
        $this->getopt->init($opts);
        
        $this->getopt->load([
            'abc',
            'def',
            '-f', // no param, should fail
        ]);
    }
    
    public function testLoad_shortOptionParamRejected()
    {
        $opts = [
            'foo_bar' => [
                'long' => 'foo-bar',
                'short' => 'f',
                'param' => Option::PARAM_REJECTED,
            ],
        ];
        
        $this->getopt->init($opts);
        
        $this->getopt->load([
            'abc',
            '-f', // has param, should flag as true
            'def',
        ]);
        
        $actual = $this->getopt->getOptionValue('foo_bar');
        $this->assertTrue($actual);
    }
    
    public function testLoad_shortOptionCluster()
    {
        $opts = [
            'foo_bar' => [
                'short' => 'f',
            ],
            'baz_dib' => [
                'short' => 'b',
            ],
            'zim_gir' => [
                'short' => 'z',
            ],
        ];
        
        $this->getopt->init($opts);
        
        $this->getopt->load([
            'abc',
            '-fbz', // should flag each as true
            'def',
        ]);
        
        $expect = [
            'foo_bar' => true,
            'baz_dib' => true,
            'zim_gir' => true,
        ];
        
        $actual = $this->getopt->getOptionValues();
        
        $this->assertSame($expect, $actual);
    }
    
    public function testLoad_shortOptionCluster_nonStrict()
    {
        $this->getopt->init([], Getopt::NON_STRICT);
        
        $this->getopt->load([
            'abc',
            '-fbz', // should flag each as true
            'def',
        ]);
        
        $expect = [];
        $actual = $this->getopt->getOptionValues();
        $this->assertSame($expect, $actual);
    }
    
    /**
     * @expectedException Aura\Cli\Exception\OptionParamRequired
     */
    public function testLoad_shortOptionClusterRequired()
    {
        $opts = [
            'foo_bar' => [
                'short' => 'f',
            ],
            'baz_dib' => [
                'short' => 'b',
                'param' => Option::PARAM_REQUIRED,
            ],
            'zim_gir' => [
                'short' => 'z',
            ],
        ];
        
        $this->getopt->init($opts);
        
        $this->getopt->load([
            'abc',
            '-fbz', // -b requires a param
            'def',
        ]);
    }
    
    public function testMagicGet()
    {
        $opts = [
            'foo_bar' => [
                'long' => 'foo-bar',
                'short' => 'f',
            ],
        ];
        
        $this->getopt->init($opts);
        
        $this->getopt->load([
            'abc',
            '--foo-bar=zim', // has param, should flag as true
            'def',
        ]);
        
        $expect = 'zim';
        $actual = $this->getopt->__get('foo_bar');
        $this->assertSame($expect, $actual);
    }
    
    public function testMagicGetNonStrict()
    {
        $opts = [
            'foo_bar' => [
                'long' => 'foo-bar',
                'short' => 'f',
            ],
        ];
        
        $this->getopt->init($opts, Getopt::NON_STRICT);
        
        $this->getopt->load([
            'abc',
            '--foo-bar=zim', // has param, should flag as true
            'def',
        ]);
        
        $actual = $this->getopt->__get('no_such_option');
        $this->assertNull($actual);
    }
}
