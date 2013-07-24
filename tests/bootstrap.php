<?php
// autoloader
require dirname(__DIR__) . '/autoload.php';

// default globals
if (is_readable(__DIR__ . '/globals.default.php')) {
    require __DIR__ . '/globals.default.php';
}

// override globals
if (is_readable(__DIR__ . '/globals.php')) {
    require __DIR__ . '/globals.php';
}
