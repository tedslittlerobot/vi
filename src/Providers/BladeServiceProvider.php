<?php namespace Vi\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class BladeServiceProvider extends ServiceProvider {

	/**
	 * Define the routes for the application.
	 *
	 * @return void
	 */
	public function map()
	{
		$this->loadRoutesFrom(app_path('Http/routes.php'));
	}

	/**
	 * Boot the service provider
	 *
	 * @return void
	 */
	public function boot()
	{
		$blade = $this->app['view']->getEngineResolver()->resolve('blade')->getCompiler();

		$tags = [
			'IfUserCan',
		];

		foreach( $tags as $tag )
		{
			$method = "compile{$tag}";
			$blade->extend( $this->{$method}() );
		}
	}

	/**
	 * Compile the ifusercan blade tag
	 *
	 * @return void
	 */
	public function compileIfUserCan()
	{
		/**
		 * @param string|array $permissions
		 *
		 * @ifusercan( $permissions )
		 *   something here
		 * @endif
		 */
		return function($view, $compiler)
		{
			// @ifusercan('permission')
			$view = preg_replace(
				$compiler->createMatcher('ifusercan'),
				'$1<?php if(Auth::user() && Auth::user()->can($2)): ?>',
				$view
			);
			return $view;
		});
	}

}
