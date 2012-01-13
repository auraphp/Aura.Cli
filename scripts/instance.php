<?php
namespace Aura\Cli;
require_once dirname(__DIR__) . '/src.php';
use Aura\Cli\Context;
use Aura\Cli\Stdio;
use Aura\Cli\Vt100;
use Aura\Cli\Getopt;
use Aura\Cli\OptionFactory;

$context = new Context();
$vt100 = new Vt100();
$stdio = new Stdio( 
    fopen('php://stdin', 'r'),
    fopen('php://stdout', 'w+'),
    fopen('php://stderr', 'w+'),
    $vt100
);
$getopt  = new Getopt( new OptionFactory());
