<?php

use JonErickson\ServerlessForLaravel\Runtime\StorageDirectories;
use JonErickson\ServerlessForLaravel\Runtime\Secrets;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;

/*
|--------------------------------------------------------------------------
| Inject SSM Secrets Into Environment
|--------------------------------------------------------------------------
|
| Next, we will inject any of the application's secrets stored in AWS
| SSM into the environment variables. These variables may be a bit
| larger than the variables allowed by Lambda which has a limit.
|
*/

// Make sure the SSM path is declared
if(!empty($_ENV['APP_SECRETS_SSM_PATH'])) {
	fwrite(STDERR, 'Preparing to add secrets to runtime'.PHP_EOL);

	// Add the secrets to our environment
	$secrets = Secrets::addToEnvironment(
		$_ENV['APP_SECRETS_SSM_PATH']
	);
}

/*
|--------------------------------------------------------------------------
| Storage Directories
|--------------------------------------------------------------------------
|
| Lambda's only writable directory is /tmp. To allow Laravel to
| function properly, we need to tell Laravel to move all directories
| to /tmp.
|
*/

// Setup storage directories
StorageDirectories::createDirectories();
StorageDirectories::configureEnvironmentVariables();

/*
|--------------------------------------------------------------------------
| Cache Configuration
|--------------------------------------------------------------------------
|
| To give the application a speed boost, we are going to cache all of the
| configuration files into a single file. The file will be loaded once
| by the runtime then it will read the configuration values from it.
|
*/

// Get the app root path
$appRoot = getenv('LAMBDA_TASK_ROOT');

// Update laravel to use the new path for storage
with(require $appRoot.'/bootstrap/app.php', function ($app) {

	// Tell laravel to use the new storage path
	$app->useStoragePath(StorageDirectories::PATH);

	fwrite(STDERR, 'Caching Laravel configuration'.PHP_EOL);

	// Cache our configuration
	$app->make(ConsoleKernelContract::class)->call('config:cache');
});