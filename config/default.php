<?php
/**
 * Loader
 */
$loader->add('Aura\Cli\\', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src');

/**
 * Services
 */
$di->set('cli_context', $di->lazyNew('Aura\Cli\Context'));
$di->set('cli_stdio', $di->lazyNew('Aura\Cli\Stdio'));

/**
 * Aura\Cli\AbstractCommand
 */
$di->params['Aura\Cli\AbstractCommand'] = [
    'context' => $di->lazyGet('cli_context'),
    'stdio'   => $di->lazyGet('cli_stdio'),
    'getopt'  => $di->lazyNew('Aura\Cli\Getopt'),
    'signal'  => $di->lazyGet('signal_manager'),
];

/**
 * Aura\Cli\Context
 */
$di->params['Aura\Cli\Context']['globals'] = $GLOBALS;

/**
 * Aura\Cli\ExceptionFactory
 */
$di->params['Aura\Cli\ExceptionFactory']['translator'] = $di->lazyCall(
    [$di->lazyGet('intl_translator_locator'), 'get'],
    'Aura.Cli'
);

/**
 * Aura\Cli\Getopt
 */
$di->params['Aura\Cli\Getopt'] = [
    'option_factory' => $di->lazyNew('Aura\Cli\OptionFactory'),
    'exception_factory' => $di->lazyNew('Aura\Cli\ExceptionFactory'),
];

/**
 * Aura\Cli\Stdio
 */
$di->params['Aura\Cli\Stdio'] = [
    'stdin' => $di->lazyNew('Aura\Cli\StdioResource', [
        'filename' => 'php://stdin',
        'mode' => 'r',
    ]),
    'stdout' => $di->lazyNew('Aura\Cli\StdioResource', [
        'filename' => 'php://stdout',
        'mode' => 'w+',
    ]),
    'stderr' => $di->lazyNew('Aura\Cli\StdioResource', [
        'filename' => 'php://stderr',
        'mode' => 'w+',
    ]),
    'vt100' => $di->lazyNew('Aura\Cli\Vt100'),
];

/**
 * Aura\Intl\PackageLocator
 */
$di->params['Aura\Intl\PackageLocator']['registry']['Aura.Cli']['en_US'] = $di->lazyCall(
    [$di->lazyGet('intl_package_factory'), 'newInstance'],
    $di->lazyRequire("$system/package/Aura.Cli/intl/en_US.php")
);
