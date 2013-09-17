<?php
namespace Aura\Cli;

use Aura\Cli\CliFactory;

class ContextTest extends \PHPUnit_Framework_TestCase
{
    protected function newContext(array $globals = [])
    {
        $factory = new CliFactory;
        return $factory->newContext($globals);
    }
    
    public function test__get()
    {
        $context = $this->newContext([
            '_ENV' => [
                'foo' => 'bar',
                'baz' => 'dib',
            ],
            '_SERVER' => [
                'zim' => 'gir',
                'irk' => 'doom',
            ],
            'argv' => [
                'a',
                '-b',
                '--cee',
            ],
        ]);
        
        // get a key
        $expect = 'gir';
        $actual = $context->server->get('zim', 'default');
        $this->assertSame($expect, $actual);
        
        // get an alternative
        $expect = 'default';
        $actual = $context->env->get('no-such-key', 'default');
        $this->assertSame($expect, $actual);
        
        // get all of one
        $expect = [
                'a',
                '-b',
                '--cee',
        ];
        $actual = $context->argv->get();
        $this->assertSame($expect, $actual);
        
        // get a nonexistent property
        $this->assertNull($context->nosuchkey);
    }
    
    public function testGetopt()
    {
        $context = $this->newContext([
            'argv' => [
                'foo',
                'bar',
                '-f',
            ]
        ]);
        
        $getopt = $context->getopt(['f:']);
        $this->assertInstanceOf('Aura\Cli\Context\Getopt', $getopt);
        
        $actual = $getopt->get();
        $expect = [
            0 => 'foo',
            1 => 'bar',
        ];
        
        $this->assertTrue($getopt->hasErrors());
        $errors = $getopt->getErrors();
        $actual = $errors[0];
        $expect = 'Aura\Cli\Exception\OptionParamRequired';
        $this->assertInstanceOf($expect, $actual);
        $expect = "The option '-f' requires a parameter.";
        $this->assertSame($expect, $actual->getMessage());
    }
}
