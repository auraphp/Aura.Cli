<?php
/**
 *
 * This file is part of Aura for PHP.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
namespace Aura\Cli;

use Aura\Cli\Context\Argv;
use Aura\Cli\Context\Env;
use Aura\Cli\Context\GetoptFactory;
use Aura\Cli\Context\GetoptParser;
use Aura\Cli\Context\OptionFactory;
use Aura\Cli\Context\Server;
use Aura\Cli\Stdio\Formatter;
use Aura\Cli\Stdio\Handle;

/**
 *
 * A factory for creating Context and Stdio objects.
 *
 * @package Aura.Cli
 *
 */
class CliFactory
{
    /**
     *
     * Returns a new Context object.
     *
     * @param array $globals A copy of $GLOBALS.
     *
     * @return Context
     *
     */
    public function newContext(array $globals)
    {
        $env    = isset($globals['_ENV'])
                ? new Env($globals['_ENV'])
                : new Env;

        $server = isset($globals['_SERVER'])
                ? new Server($globals['_SERVER'])
                : new Server;

        $argv   = isset($globals['argv'])
                ? new Argv($globals['argv'])
                : new Argv;

        $getopt_factory = new GetoptFactory(new GetoptParser(new OptionFactory));

        return new Context(
            $env,
            $server,
            $argv,
            $getopt_factory
        );
    }

    /**
     *
     * Returns a new Stdio object.
     *
     * @param string $stdin The resource to open for stdin.
     *
     * @param string $stdout The resource to open for stdout.
     *
     * @param string $stderr The resource to open for stderr.
     *
     * @return Stdio
     *
     */
    public function newStdio(
        $stdin  = 'php://stdin',
        $stdout = 'php://stdout',
        $stderr = 'php://stderr'
    ) {
        return new Stdio(
            new Handle($stdin, 'r'),
            new Handle($stdout, 'w+'),
            new Handle($stderr, 'w+'),
            new Formatter
        );
    }
}
