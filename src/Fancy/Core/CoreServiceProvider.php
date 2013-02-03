<?php namespace Fancy\Core;

use Fancy\Core\Support\Factory;
use Fancy\Core\Support\Wordpress;
use Fancy\Core\Support\ViewFile;
use Illuminate\Support\ServiceProvider;

define ('FANCY_PACKAGE', 'fancy/core');
define ('FANCY_NAME', 'fancy');

class CoreServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package(FANCY_PACKAGE, FANCY_NAME);

        include __DIR__.'/../../routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $namespace = FANCY_NAME;

		$this->app[$namespace] = $this->app->share(function($app) {
            return new Factory($app);
        });

        $this->app["$namespace.wordpress"] = $this->app->share(function($app) {
            return new Wordpress;
        });

        $this->app["$namespace.view-file"] = $this->app->share(function($app) {
            return new ViewFile;
        });

        $this->app["$namespace.view"] = $this->app->share(function($app) use ($namespace) {
            return $app["$namespace.view-file"]->setContext(null);
        });

        $this->app["$namespace.layout"] = $this->app->share(function($app) use ($namespace) {
            return $app["$namespace.view-file"]->setContext('layouts');
        });
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
