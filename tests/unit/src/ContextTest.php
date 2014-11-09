<?php
namespace Aura\Cli;

class ContextTest extends \PHPUnit_Framework_TestCase
{
    protected function newContext(array $globals = array())
    {
        $factory = new CliFactory;
        return $factory->newContext($globals);
    }

    public function test__get()
    {
        $context = $this->newContext(array(
            '_ENV' => array(
                'foo' => 'bar',
                'baz' => 'dib',
            ),
            '_SERVER' => array(
                'zim' => 'gir',
                'irk' => 'doom',
            ),
            'argv' => array(
                'a',
                '-b',
                '--cee',
            ),
        ));

        // get a server value
        $expect = 'gir';
        $actual = $context->server->get('zim', 'default');
        $this->assertSame($expect, $actual);

        // get an alternative server value
        $expect = 'default';
        $actual = $context->server->get('no-such-key', 'default');
        $this->assertSame($expect, $actual);

        // get an env key
        $expect = 'bar';
        $actual = $context->env->get('foo', 'default');
        $this->assertSame($expect, $actual);

        // get an alternative env key
        $expect = 'default';
        $actual = $context->env->get('no-such-key', 'default');
        $this->assertSame($expect, $actual);

        // get from putenv()
        putenv('PUTFOO=foo');
        $expect = 'foo';
        $actual = $context->env->get('PUTFOO');
        $this->assertSame($expect, $actual);
        $this->assertNull($context->env->get('PUTBAR'));

        // get all of one
        $expect = array(
                'a',
                '-b',
                '--cee',
        );
        $actual = $context->argv->get();
        $this->assertSame($expect, $actual);
    }

    public function testGetopt()
    {
        $context = $this->newContext(array(
            'argv' => array(
                'foo',
                'bar',
                '-f',
            )
        ));

        $getopt = $context->getopt(array('f:'));
        $this->assertInstanceOf('Aura\Cli\Context\Getopt', $getopt);

        $actual = $getopt->get();
        $expect = array(
            0 => 'foo',
            1 => 'bar',
        );

        $this->assertTrue($getopt->hasErrors());
        $errors = $getopt->getErrors();
        $actual = $errors[0];
        $expect = 'Aura\Cli\Exception\OptionParamRequired';
        $this->assertInstanceOf($expect, $actual);
        $expect = "The option '-f' requires a parameter.";
        $this->assertSame($expect, $actual->getMessage());
    }

    /**
     * Test that when using a getopt alias that whichever order you use
     * them in the result is the same
     */
    public function testGetoptAlias()
    {
        $context = $this->newContext(array(
            'argv' => array(
                '-f',
            )
        ));

        $getopt = $context->getopt(array('f,foo'));
        $this->assertInstanceOf('Aura\Cli\Context\Getopt', $getopt);

        $this->assertTrue($getopt->get('-f'));
        $this->assertTrue($getopt->get('--foo'));

        $getopt = $context->getopt(array('foo,f'));
        $this->assertInstanceOf('Aura\Cli\Context\Getopt', $getopt);

        $this->assertTrue($getopt->get('-f'));
        $this->assertTrue($getopt->get('--foo'));

        $context = $this->newContext(array(
            'argv' => array(
                '-foo',
            )
        ));

        $getopt = $context->getopt(array('f,foo'));
        $this->assertInstanceOf('Aura\Cli\Context\Getopt', $getopt);

        $this->assertTrue($getopt->get('-f'));
        $this->assertTrue($getopt->get('--foo'));

        $getopt = $context->getopt(array('foo,f'));
        $this->assertInstanceOf('Aura\Cli\Context\Getopt', $getopt);

        $this->assertTrue($getopt->get('-f'));
        $this->assertTrue($getopt->get('--foo'));
    }

    public function testGithubIssue32()
    {
        $context = $this->newContext();
        $getopt  = $context->getopt(array('f:'));
        $actual = $context->getopt_factory->getGetoptParser()->getOptions();
        $expect = array(
            '-f' => (object) array(
                'name' => '-f',
                'alias' => null,
                'multi' => false,
                'param' => 'required',
                'descr' => null,
            ),
        );
        $this->assertEquals($expect, $actual);
    }
}
