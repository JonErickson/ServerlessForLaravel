<?php

namespace JonErickson\ServerlessForLaravel;

use JonErickson\ServerlessForLaravel\Runtime\StorageDirectories;
use Illuminate\Support\ServiceProvider;

class ServerlessForLaravelServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// Publush the serverless.yml config file
		if ($this->app->runningInConsole()) {
			$this->publishes([
				__DIR__.'/../config/serverless.yml' => $this->app['path.base'].DIRECTORY_SEPARATOR.'serverless.yml',
			], 'serverlessforlaravel');
		}
	}

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // If we are running in lambda
        if (!empty($_ENV['LAMBDA_TASK_ROOT'])) {
            // Tell Laravel to use new storage path
            $this->app->useStoragePath(StorageDirectories::PATH);
        }
    }
}
