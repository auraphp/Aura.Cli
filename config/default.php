<?php
/**
 * Package prefix for autoloader.
 */
$loader->add('Aura\Cli\\', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src');

/**
 * Instance params and setter values.
 */

// Getopt
$di->params['Aura\Cli\Getopt']['option_factory'] = $di->lazyNew('Aura\Cli\OptionFactory');

// Command
$di->params['Aura\Cli\Command']['context'] = $di->lazyGet('cli_context');
$di->params['Aura\Cli\Command']['stdio']   = $di->lazyGet('cli_stdio');
$di->params['Aura\Cli\Command']['getopt']  = $di->lazyNew('Aura\Cli\Getopt');
$di->params['Aura\Cli\Command']['signal']  = $di->lazyGet('signal_manager');

/**
 * Dependency services.
 */
$di->set('cli_context', function() use ($di) {
    return $di->newInstance('Aura\Cli\Context');
});

$di->set('cli_stdio', function() use ($di) {
    $vt100 = $di->newInstance('Aura\Cli\Vt100');
    return $di->newInstance('Aura\Cli\Stdio', [
        'stdin'  => fopen('php://stdin', 'r'),
        'stdout' => fopen('php://stdout', 'w+'),
        'stderr' => fopen('php://stderr', 'w+'),
        'vt100'  => $vt100,
    ]);
});

$di->set('cli_command_factory', function() use ($di) {
    return $di->newInstance('Aura\Cli\CommandFactory', [
        'forge'  => $di->getForge(),
    ]);
});
