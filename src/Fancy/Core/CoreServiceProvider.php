<?php namespace Fancy\Core;

use Fancy\Core\Support\Factory;
use Fancy\Core\Support\Wordpress;
use Fancy\Core\Support\ViewFile;
use Fancy\Core\Support\Asset;
use Fancy\Core\Support\Custom;
use Fancy\Core\Facade\Core;

use Illuminate\Support\ServiceProvider;
use Doctrine\Common\Inflector\Inflector;

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

        if($this->app['env'] !== 'testing') {
            Core::asset()->initialize();
            Core::custom()->initialize();
        }
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

        $this->app["$namespace.view-file"] = $this->app->share(function($app) use ($namespace) {
            $wordpress = $app["$namespace.wordpress"];
            $finder = $app['view.finder'];

            return new ViewFile($wordpress, $finder);
        });

        $this->app["$namespace.view"] = $this->app->share(function($app) use ($namespace) {
            return $app["$namespace.view-file"]->setDirectory(null);
        });

        $this->app["$namespace.layout"] = $this->app->share(function($app) use ($namespace) {
            return $app["$namespace.view-file"]->setDirectory('layouts');
        });

        $this->app["$namespace.asset"] = $this->app->share(function($app) use ($namespace) {

            $wordpress = $app["$namespace.wordpress"];
            $config = \Config::get("$namespace::asset");

            $asset = new Asset($wordpress, $config);
            return $asset;
        });

        $this->app["$namespace.custom"] = $this->app->share(function($app) use ($namespace) {

            $inflector = new Inflector();
            $wordpress = $app["$namespace.wordpress"];
            $config = \Config::get("$namespace::custom");

            $custom = new Custom($inflector, $wordpress, $config);
            return $custom;
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
