<?php
namespace Aura\Cli;

use Aura\Cli\Context\ValuesFactory;
use Aura\Cli\Context\Getopt;

class ContextTest extends \PHPUnit_Framework_TestCase
{
    protected function newContext(array $globals = [])
    {
        $globals = array_merge($GLOBALS, $globals);
        return new Context(new ValuesFactory(new Getopt, $globals));
    }
    
    public function testGlobalValues()
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
    }
    
    public function testGetopt()
    {
        $opt_defs = ['f'];
        $arg_defs = ['arg0', 'arg1', 'arg2'];
        
        $context = $this->newContext(['argv' => [
        ]]);
        
        $getopt = $context->getopt($opt_defs, $arg_defs);
        
        $this->assertInstanceOf('Aura\Cli\Context\GetoptValues', $getopt);
        
    }
}
