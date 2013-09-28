<?php
/**
 * Services
 */
$di->set('cli_context', $di->lazyNew('Aura\Cli\Context'));
$di->set('cli_stdio', $di->lazyNew('Aura\Cli\Stdio'));

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
