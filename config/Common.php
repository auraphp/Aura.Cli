<?php
namespace Aura\Cli\_Config;

use Aura\Di\Config;
use Aura\Di\Container;

class Common extends Config
{
    public function define(Container $di)
    {
        /**
         * Aura\Cli\Context
         */
        $di->params['Aura\Cli\Context'] = array(
            'env'    => $di->lazyNew('Aura\Cli\Context\Env'),
            'server' => $di->lazyNew('Aura\Cli\Context\Server'),
            'argv'   => $di->lazyNew('Aura\Cli\Context\Argv'),
            'getopt' => $di->lazyNew('Aura\Cli\Context\Getopt'),
        );

        /**
         * Aura\Cli\Context\Argv
         */
        $di->params['Aura\Cli\Context\Argv'] = array(
            'values' => (isset($_SERVER['argv']) ? $_SERVER['argv'] : array()),
        );

        /**
         * Aura\Cli\Context\Env
         */
        $di->params['Aura\Cli\Context\Env'] = array(
            'values' => $_ENV,
        );

        /**
         * Aura\Cli\Context\Server
         */
        $di->params['Aura\Cli\Context\Server'] = array(
            'values' => $_SERVER,
        );

        /**
         * Aura\Cli\Stdio
         */
        $di->params['Aura\Cli\Stdio'] = array(
            'stdin' => $di->lazyNew('Aura\Cli\Stdio\Handle', array(
                'name' => 'php://stdin',
                'mode' => 'r',
            )),
            'stdout' => $di->lazyNew('Aura\Cli\Stdio\Handle', array(
                'name' => 'php://stdout',
                'mode' => 'w+',
            )),
            'stderr' => $di->lazyNew('Aura\Cli\Stdio\Handle', array(
                'name' => 'php://stderr',
                'mode' => 'w+',
            )),
            'formatter' => $di->lazyNew('Aura\Cli\Stdio\Formatter'),
        );
    }
}