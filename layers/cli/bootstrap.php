<?php declare(strict_types=1);

use Bref\Context\Context;
use Bref\Runtime\LambdaRuntime;
use Symfony\Component\Process\Process;

// Display all errors
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Get app root
$appRoot = getenv('LAMBDA_TASK_ROOT');

// Required vendor and bootstrap file
require $appRoot . '/vendor/autoload.php';
require __DIR__ . '/laravelBootstrap.php';

// Get lambda runtime
$lambdaRuntime = LambdaRuntime::fromEnvironmentVariable('console');

// Handle invocations
while (true) {
	$lambdaRuntime->processNextEvent(function ($event, Context $context) use ($handlerFile): array {
		if (is_array($event)) {
			// Backward compatibility with the former CLI invocation format
			$cliOptions = $event['cli'] ?? '';
		} elseif (is_string($event)) {
			$cliOptions = $event;
		} else {
			$cliOptions = '';
		}

		// Create a new process
		$timeout = max(1, $context->getRemainingTimeInMillis() / 1000 - 1);
		$command = sprintf('/opt/bin/php %s %s 2>&1', $handlerFile, $cliOptions);
		$process = Process::fromShellCommandline($command, null, ['LAMBDA_INVOCATION_CONTEXT' => json_encode($context)], null, $timeout);

		// Run the command
		$process->run(function ($type, $buffer) {
			echo $buffer;
		});

		// Get the exit code
		$exitCode = $process->getExitCode();

		// If we didn't successfully exit
		if ($exitCode > 0) {
			throw new Exception('The command exited with a non-zero status code: ' . $exitCode);
		}

		// Return output
		return [
			'exitCode' => $exitCode, // will always be 0
			'output' => $process->getOutput(),
		];
	});
}