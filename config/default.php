<?php
/**
 * Services
 */
$di->set('cli_context', $di->lazyNew('Aura\Cli\Context'));
$di->set('cli_stdio', $di->lazyNew('Aura\Cli\Stdio'));

/**
 * Aura\Cli\Context
 */
$di->params['Aura\Cli\Context'] = [
    'values_factory' => $di->lazyNew('Aura\Cli\Context\ValuesFactory'),
];

/**
 * Aura\Cli\Context\ValuesFactory
 */
$di->params['Aura\Cli\Context\ValuesFactory']['globals'] = $GLOBALS;

/**
 * Aura\Cli\Stdio
 */
$di->params['Aura\Cli\Stdio'] = [
    'stdin' => $di->lazyNew('Aura\Cli\Stdio\Handle', [
        'name' => 'php://stdin',
        'mode' => 'r',
    ]),
    'stdout' => $di->lazyNew('Aura\Cli\Stdio\Handle', [
        'name' => 'php://stdout',
        'mode' => 'w+',
    ]),
    'stderr' => $di->lazyNew('Aura\Cli\Stdio\Handle', [
        'name' => 'php://stderr',
        'mode' => 'w+',
    ]),
    'vt100' => $di->lazyNew('Aura\Cli\Vt100'),
];
