<?php

namespace JonErickson\ServerlessForLaravel;

use JonErickson\ServerlessForLaravel\Runtime\StorageDirectories;
use Illuminate\Support\ServiceProvider;

class ServerlessForLaravelServiceProvider extends ServiceProvider
{
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