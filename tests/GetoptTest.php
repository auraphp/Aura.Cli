<?php
namespace Aura\Cli;
use Aura\Di\Config as Config;
use Aura\Di\Builder as Builder;
use Aura\Di\Forge as Forge;

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
        $config = new Config;
        $forge = new Forge($config);
        $this->option_factory = new OptionFactory($forge);
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
        $opts = array(
            'foo_bar' => array(
                'long' => 'foo-bar',
                'short' => 'f',
            ),
            'baz_dib' => array(
                'long' => 'baz-dib',
                'short' => 'b',
            ),
        );
        
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
        $opts = array(
            'foo_bar' => array(
                'long' => 'foo-bar',
                'short' => 'f',
            ),
            'baz_dib' => array(
                'long' => 'baz-dib',
                'short' => 'b',
            ),
        );
        
        $this->getopt->init($opts);
        $this->getopt->init($opts);
    }
    
    /**
     * @expectedException \UnexpectedValueException
     */
    public function testInit_unexpected()
    {
        $opts = array(
            'foo_bar' => true,
            'baz_dib' => true,
        );
        
        $this->getopt->init($opts);
    }
    
    public function testGetOptions()
    {
        $this->testInit();
        
        $expect = array();
        $expect['foo_bar'] = $this->option_factory->newInstance(array(
            'name' => 'foo_bar',
            'long' => 'foo-bar',
            'short' => 'f',
        ));
        
        $expect['baz_dib'] = $this->option_factory->newInstance(array(
            'name' => 'baz_dib',
            'long' => 'baz-dib',
            'short' => 'b',
        ));
        
        $actual = $this->getopt->getOptions();
        
        $this->assertEquals($expect['foo_bar'], $actual['foo_bar']);
        $this->assertEquals($expect['baz_dib'], $actual['baz_dib']);
    }
    
    public function testGetOption()
    {
        $this->testInit();
        
        $expect = $this->option_factory->newInstance(array(
            'name' => 'foo_bar',
            'long' => 'foo-bar',
            'short' => 'f',
        ));
        
        $actual = $this->getopt->getOption('foo_bar');
        
        $this->assertEquals($expect, $actual);
    }
    
    /**
     * @expectedException Aura\Cli\Exception_OptionNotDefined
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
        
        $expect = $this->option_factory->newInstance(array(
            'name' => 'foo_bar',
            'long' => 'foo-bar',
            'short' => 'f',
        ));
        
        $actual = $this->getopt->getLongOption('foo-bar');
        
        $this->assertEquals($expect, $actual);
    }

    /**
     * @expectedException Aura\Cli\Exception_OptionNotDefined
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
        
        $expect = $this->option_factory->newInstance(array(
            'name' => 'foo_bar',
            'long' => 'foo-bar',
            'short' => 'f',
        ));
        
        $actual = $this->getopt->getShortOption('f');
        
        $this->assertEquals($expect, $actual);
    }

    /**
     * @expectedException Aura\Cli\Exception_OptionNotDefined
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
        
        $this->getopt->load(array(
            'abc',
            'def',
        ));
        
        // check options
        $expect = array(
            'foo_bar' => null,
            'baz_dib' => null,
        );
        
        $actual = $this->getopt->getOptionValues();
        $this->assertSame($expect, $actual);
        
        // check params
        $expect = array(
            'abc',
            'def',
        );
        $actual = $this->getopt->getParams();
        $this->assertSame($expect, $actual);
    }
    
    public function testLoad_longOptions()
    {
        $this->testInit();
        
        $this->getopt->load(array(
            'abc',
            '--foo-bar=zim',
            'def',
            '--baz-dib=gir',
        ));
        
        // check options
        $expect = array(
            'foo_bar' => 'zim',
            'baz_dib' => 'gir',
        );
        
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
        $expect = array(
            'abc',
            'def',
        );
        $actual = $this->getopt->getParams();
        $this->assertSame($expect, $actual);
    }
    
    public function testLoad_longOptions_nonStrict()
    {
        $this->testInit(Getopt::NON_STRICT);
        
        $this->getopt->load(array(
            'abc',
            '--no-such-option=zim',
        ));
        
        // check single option values
        $actual = $this->getopt->getOptionValue('no_such_option');
        $this->assertNull($actual);
    }
    
    public function testLoad_shortOptions()
    {
        $this->testInit();
        
        $this->getopt->load(array(
            'abc',
            '-f',
            'zim',
            'def',
            '-b',
            'gir',
        ));
        
        // check options
        $expect = array(
            'foo_bar' => 'zim',
            'baz_dib' => 'gir',
        );
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
        $expect = array(
            'abc',
            'def',
        );
        $actual = $this->getopt->getParams();
        $this->assertSame($expect, $actual);
    }
    
    public function testLoad_shortOptions_nonStrict()
    {
        $this->testInit(Getopt::NON_STRICT);
        
        $this->getopt->load(array(
            'abc',
            '-z',
        ));
        
        // check single option values
        $actual = $this->getopt->getOptionValue('no_such_option');
        $this->assertNull($actual);
    }
    
    public function testLoad_mixedOptionsAndParams()
    {
        $this->testInit();
        
        $this->getopt->load(array(
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
        ));
        
        // check options
        $expect = array(
            'foo_bar' => 'zim',
            'baz_dib' => 'gir',
        );
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
        $expect = array(
            'abc',
            'def',
            '--no-such-option=123',
            '-n',
            '456',
            'ghi',
        );
        $actual = $this->getopt->getParams();
        $this->assertSame($expect, $actual);
    }
    
    public function testLoad_optionalParamsAsFlags()
    {
        $this->testInit();
        
        $this->getopt->load(array(
            'abc',
            '--foo-bar',
            'def',
            '-b',
        ));
        
        // check options
        $expect = array(
            'foo_bar' => true,
            'baz_dib' => true,
        );
        $actual = $this->getopt->getOptionValues();
        $this->assertSame($expect, $actual);
        
        // check single option values
        $actual = $this->getopt->getOptionValue('foo_bar');
        $this->assertTrue($actual);
        
        $actual = $this->getopt->getOptionValue('baz_dib');
        $this->assertTrue($actual);
        
        // check params
        $expect = array(
            'abc',
            'def',
        );
        $actual = $this->getopt->getParams();
        $this->assertSame($expect, $actual);
    }
    
    /**
     * @expectedException Aura\Cli\Exception_OptionParamRequired
     */
    public function testLoad_longOptionParamRequired()
    {
        $opts = array(
            'foo_bar' => array(
                'long' => 'foo-bar',
                'short' => 'f',
                'param' => Option::PARAM_REQUIRED,
            ),
        );
        
        $this->getopt->init($opts);
        
        $this->getopt->load(array(
            'abc',
            '--foo-bar', // no param, should fail
            'def',
        ));
    }
    
    /**
     * @expectedException Aura\Cli\Exception_OptionParamRejected
     */
    public function testLoad_longOptionParamRejected()
    {
        $opts = array(
            'foo_bar' => array(
                'long' => 'foo-bar',
                'short' => 'f',
                'param' => Option::PARAM_REJECTED,
            ),
        );
        
        $this->getopt->init($opts);
        
        $this->getopt->load(array(
            'abc',
            '--foo-bar=zim', // has param, should fail
            'def',
        ));
    }
    
    /**
     * @expectedException Aura\Cli\Exception_OptionParamRequired
     */
    public function testLoad_shortOptionParamRequired()
    {
        $opts = array(
            'foo_bar' => array(
                'long' => 'foo-bar',
                'short' => 'f',
                'param' => Option::PARAM_REQUIRED,
            ),
        );
        
        $this->getopt->init($opts);
        
        $this->getopt->load(array(
            'abc',
            'def',
            '-f', // no param, should fail
        ));
    }
    
    public function testLoad_shortOptionParamRejected()
    {
        $opts = array(
            'foo_bar' => array(
                'long' => 'foo-bar',
                'short' => 'f',
                'param' => Option::PARAM_REJECTED,
            ),
        );
        
        $this->getopt->init($opts);
        
        $this->getopt->load(array(
            'abc',
            '-f', // has param, should flag as true
            'def',
        ));
        
        $actual = $this->getopt->getOptionValue('foo_bar');
        $this->assertTrue($actual);
    }
    
    public function testLoad_shortOptionCluster()
    {
        $opts = array(
            'foo_bar' => array(
                'short' => 'f',
            ),
            'baz_dib' => array(
                'short' => 'b',
            ),
            'zim_gir' => array(
                'short' => 'z',
            ),
        );
        
        $this->getopt->init($opts);
        
        $this->getopt->load(array(
            'abc',
            '-fbz', // should flag each as true
            'def',
        ));
        
        $expect = array(
            'foo_bar' => true,
            'baz_dib' => true,
            'zim_gir' => true,
        );
        
        $actual = $this->getopt->getOptionValues();
        
        $this->assertSame($expect, $actual);
    }
    
    public function testLoad_shortOptionCluster_nonStrict()
    {
        $this->getopt->init(array(), Getopt::NON_STRICT);
        
        $this->getopt->load(array(
            'abc',
            '-fbz', // should flag each as true
            'def',
        ));
        
        $expect = array();
        $actual = $this->getopt->getOptionValues();
        $this->assertSame($expect, $actual);
    }
    
    /**
     * @expectedException Aura\Cli\Exception_OptionParamRequired
     */
    public function testLoad_shortOptionClusterRequired()
    {
        $opts = array(
            'foo_bar' => array(
                'short' => 'f',
            ),
            'baz_dib' => array(
                'short' => 'b',
                'param' => Option::PARAM_REQUIRED,
            ),
            'zim_gir' => array(
                'short' => 'z',
            ),
        );
        
        $this->getopt->init($opts);
        
        $this->getopt->load(array(
            'abc',
            '-fbz', // -b requires a param
            'def',
        ));
    }
    
    public function testMagicGet()
    {
        $opts = array(
            'foo_bar' => array(
                'long' => 'foo-bar',
                'short' => 'f',
            ),
        );
        
        $this->getopt->init($opts);
        
        $this->getopt->load(array(
            'abc',
            '--foo-bar=zim', // has param, should flag as true
            'def',
        ));
        
        $expect = 'zim';
        $actual = $this->getopt->__get('foo_bar');
        $this->assertSame($expect, $actual);
    }
    
    public function testMagicGetNonStrict()
    {
        $opts = array(
            'foo_bar' => array(
                'long' => 'foo-bar',
                'short' => 'f',
            ),
        );
        
        $this->getopt->init($opts, Getopt::NON_STRICT);
        
        $this->getopt->load(array(
            'abc',
            '--foo-bar=zim', // has param, should flag as true
            'def',
        ));
        
        $actual = $this->getopt->__get('no_such_option');
        $this->assertNull($actual);
    }
}
