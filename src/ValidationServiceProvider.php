<?php namespace Bsapaka\ScalableTaxonomy;

use Illuminate\Support\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->app->singleton('validator', function ($app) {
			$validator = new ValidatorFactory($app['translator'], $app);

			if (isset($app['validation.presence'])) {
				$validator->setPresenceVerifier($app['validation.presence']);
			}

			return $validator;
		});
	}
	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}
}
