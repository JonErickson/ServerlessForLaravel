<?php declare(strict_types=1);

use Bref\Bref;
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
$lambdaRuntime = LambdaRuntime::fromEnvironmentVariable('function');

// Get the container
$container = Bref::getContainer();

// Check to make sure our handler exists
try {
	$handler = $container->get(getenv('_HANDLER'));
} catch (Throwable $e) {
	$lambdaRuntime->failInitialization($e->getMessage());
}

// Define our loop max
$loopMax = getenv('BREF_LOOP_MAX') ?: 1;
$loops = 0;

// Handle invocation requests
while (true) {
	if (++$loops > $loopMax) {
		exit(0);
	}
	$success = $lambdaRuntime->processNextEvent($handler);
	// In case the execution failed, we force starting a new process regardless of BREF_LOOP_MAX
	// Why: an exception could have left the application in a non-clean state, this is preventive
	if (! $success) {
		exit(0);
	}
}