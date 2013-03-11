<?php namespace Fancy\Core;

use Fancy\Core\Support\Factory;
use Fancy\Core\Support\Wordpress;
use Fancy\Core\View\ViewFile;
use Fancy\Core\Support\Asset;
use Fancy\Core\Support\Custom;
use Fancy\Core\Facade\Core;
use Fancy\Core\Model\WpPost;

use Illuminate\Support\ServiceProvider;
use Doctrine\Common\Inflector\Inflector;

use Fancy\Core\View\BladeCompiler;
use Fancy\Core\View\CompilerEngine;

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

        if(Wordpress::available()) {
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

            if(is_null($config)) {
                $config = array();
            }

            $asset = new Asset($wordpress, $config);
            return $asset;
        });

        $this->app["$namespace.custom"] = $this->app->share(function($app) use ($namespace) {

            $wordpress = $app["$namespace.wordpress"];
            $config = \Config::get("$namespace::custom");

            if(is_null($config)) {
                $config = array();
            }

            $custom = new Custom($wordpress, $config);
            return $custom;
        });

        $this->app["$namespace.wpPost"] = $this->app->share(function($app) use ($namespace) {
            $wordpress = $app["$namespace.wordpress"];

            WpPost::setWordpress($wordpress);

            return WpPost::cast();
        });

        $app = $this->app;

        $this->app["view"]->addExtension('blade.php', 'blade', function() use ($app){
            $cache = $app['path'].'/storage/views';

            // The Compiler engine requires an instance of the CompilerInterface, which in
            // this case will be the Blade compiler, so we'll first create the compiler
            // instance to pass into the engine so it can compile the views properly.
            $compiler = new BladeCompiler($app['files'], $cache);

            return new CompilerEngine($compiler, $app['files']);
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
