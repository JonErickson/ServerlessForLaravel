<?php declare(strict_types=1);

// Display all errors
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Required vendor and bootstrap file
require $appRoot . '/vendor/autoload.php';
require __DIR__ . '/laravelBootstrap.php';