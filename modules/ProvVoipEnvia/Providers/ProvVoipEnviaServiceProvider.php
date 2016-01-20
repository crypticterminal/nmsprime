<?php namespace Modules\provvoipenvia\Providers;

use Illuminate\Support\ServiceProvider;

class ProvVoipEnviaServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		/* \View::addLocation(__DIR__.'/../Resources/views'); */

		\View::addNamespace('provvoipenvia', __DIR__.'/../Resources/views');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
