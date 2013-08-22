<?php
namespace Aura\Cli;

use Aura\Cli\Context\PropertyFactory;

class ContextTest extends \PHPUnit_Framework_TestCase
{
    protected function newContext(array $globals = [])
    {
        $globals = array_merge($GLOBALS, $globals);
        return new Context(new PropertyFactory($globals));
    }
    
    public function testValueObjects()
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
                'b',
                'c',
            ],
        ]);
        
        $expect = 'gir';
        $actual = $context->server->get('zim', 'default');
        $this->assertSame($expect, $actual);
        
        $expect = 'default';
        $actual = $context->env->get('no-such-key', 'default');
        $this->assertSame($expect, $actual);
    }
}
