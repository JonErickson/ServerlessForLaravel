<?php declare(strict_types=1);

use Bref\Event\Http\FpmHandler;
use Bref\Runtime\LambdaRuntime;

// Display all errors
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Get app root
$appRoot = getenv('LAMBDA_TASK_ROOT');

// Required vendor and bootstrap file
require $appRoot . '/vendor/autoload.php';
require __DIR__ . '/laravelBootstrap.php';

// Get lambda runtime
$lambdaRuntime = LambdaRuntime::fromEnvironmentVariable('fpm');

// Check to make sure our handler exists
$handler = $appRoot . '/' . getenv('_HANDLER');
if (! is_file($handler)) {
	$lambdaRuntime->failInitialization("Handler `$handler` doesn't exist");
}

// New PHP-FPM instance
$phpFpm = new FpmHandler($handler);

// Try and start
try {
	$phpFpm->start();
} catch (\Throwable $e) {
	$lambdaRuntime->failInitialization('Error while starting PHP-FPM', $e);
}

// Handle invocation requests
while (true) {
	$lambdaRuntime->processNextEvent($phpFpm);
}
