<?php declare(strict_types=1);

// Display all errors
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Get app root
$appRoot = getenv('LAMBDA_TASK_ROOT');

// Required vendor and bootstrap file
require $appRoot . '/vendor/autoload.php';
require __DIR__ . '/laravelBootstrap.php';