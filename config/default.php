<?php
/**
 * Instance params and setter values.
 */

// Getopt
$di->params['aura\cli\Getopt']['option_factory'] = $di->lazyNew('aura\cli\OptionFactory');

// Controller
$di->params['aura\cli\Controller']['context'] = $di->lazyGet('cli_context');
$di->params['aura\cli\Controller']['stdio']   = $di->lazyGet('cli_stdio');
$di->params['aura\cli\Controller']['getopt']  = $di->lazyNew('aura\cli\Getopt');
$di->params['aura\cli\Controller']['signal']  = $di->lazyGet('signal_manager');

/**
 * Dependency services.
 */
$di->set('cli_context', function() use ($di) {
    return $di->newInstance('aura\cli\Context');
});

$di->set('cli_stdio', function() use ($di) {
    $vt100 = $di->newInstance('aura\cli\Vt100');
    return $di->newInstance('aura\cli\Stdio', array(
        'stdin'  => fopen('php://stdin', 'r'),
        'stdout' => fopen('php://stdout', 'w+'),
        'stderr' => fopen('php://stderr', 'w+'),
        'vt100'  => $vt100,
    ));
});

$di->set('cli_controller_factory', function() use ($di) {
    return $di->newInstance('aura\cli\ControllerFactory', array(
        'forge'  => $di->getForge(),
    ));
});
