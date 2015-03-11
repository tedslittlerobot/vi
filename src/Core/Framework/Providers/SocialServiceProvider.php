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

			if ( ! $this->app['config']->has( $key ) )
			{
				abort(404, "There is no social provider: \"$name\"");
			}

			$scopes = $this->app['config']
				->get("{$key}.scopes", []);

			return $this->app[ Factory::class ]->with( $name )
				->scopes( (array) $scopes );
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
