<?php
require __DIR__ . '/autoload.php';

use Aura\Cli\CliFactory;
use Aura\Cli\Table;
use Aura\Cli\Stdio\Formatter;

$cli_factory = new CliFactory;

$table = new Table(new Formatter());
$table->setHeaders(array('<<red>>foo<<reset>>', '<<redbg>><<white>>bar<<reset>>'));

$stdio = $cli_factory->newStdio();
$stdio->out($table->getTable());
