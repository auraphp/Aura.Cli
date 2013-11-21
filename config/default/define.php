<?php
/**
 * Aura\Cli\Context
 */
$di->params['Aura\Cli\Context'] = array(
    'env'    => $_ENV,
    'server' => $_SERVER,
    'argv'   => $GLOBALS['argv'],
    'getopt' => $di->lazyNew('Aura\Cli\Context\Getopt'),
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
