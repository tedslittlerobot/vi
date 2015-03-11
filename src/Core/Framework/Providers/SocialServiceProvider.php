<?php namespace Vi\Core\Framework\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;
use Laravel\Socialite\SocialiteServiceProvider;

class SocialServiceProvider extends ServiceProvider {

	/**
	 * Boot the service provider
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->app['router']->bind('social_provider', function($name)
		{
			$key = "social.providers.{$name}";
			$config = $this->app['config'];

			if ( ! $config->has( $key ) )
			{
				abort(404, "There is no social provider: \"$name\"");
			}

			$provider = $this->app[ Factory::class ]->with( $name );

			// If set, override providers default scopes
			if ( $config->has("{$key}.scopes") )
			{
				$provider->scopes( (array) $config->get("{$key}.scopes") );
			}

			return $provider;
		});
	}

	/**
	 * @{inheritdoc}
	 */
	public function register()
	{
		$this->app->register( SocialiteServiceProvider::class );
	}

}
