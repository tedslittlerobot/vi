<?php namespace Vi;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class ViServiceProvider extends ServiceProvider {

	/**
	 * Boot the service provider
	 *
	 * @return void
	 */
	public function boot()
	{
		$publish = [];
		$name = 'vi';

		// views
		$viewPath = realpath( __DIR__.'/../resources/views' );

		$this->loadViewsFrom( $viewPath, $name );
		$publish[$viewPath] = base_path("resources/views/vendor/{$name}");

		// lang
		$langPath = realpath( __DIR__.'/../resources/lang' );

		$this->loadTranslationsFrom( $langPath, $name );
		$publish[$langPath] = base_path("reources/lang/vendor/{$name}");

		// config
		$configPath = realpath( __DIR__.'/../resources/config' );

		$this->mergeConfigFrom( "{$configPath}/social.php", 'social' );
		$publish[ "$configPath/social.php" ] = config_path("social.php");

		// migrations
		// @todo - publish migrations

		$this->publishes( $publish );
	}

	/**
	 * @{inheritdoc}
	 */
	public function register() {}

}
