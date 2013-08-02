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
    'property_factory' => $di->lazyNew('Aura\Cli\Context\PropertyFactory'),
];

/**
 * Aura\Cli\Context\PropertyFactory
 */
$di->params['Aura\Cli\Context\PropertyFactory']['globals'] = $GLOBALS;

/**
 * Aura\Cli\Stdio
 */
$di->params['Aura\Cli\Stdio'] = [
    'stdin' => $di->lazyNew('Aura\Cli\Stdio\Resource', [
        'filename' => 'php://stdin',
        'mode' => 'r',
    ]),
    'stdout' => $di->lazyNew('Aura\Cli\Stdio\Resource', [
        'filename' => 'php://stdout',
        'mode' => 'w+',
    ]),
    'stderr' => $di->lazyNew('Aura\Cli\Stdio\Resource', [
        'filename' => 'php://stderr',
        'mode' => 'w+',
    ]),
    'vt100' => $di->lazyNew('Aura\Cli\Vt100'),
];
