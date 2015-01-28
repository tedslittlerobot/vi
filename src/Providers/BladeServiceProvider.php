<?php namespace Vi\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

use Vi\Users\User;

class RouteServiceProvider extends ServiceProvider {

	/**
	 * Define the routes for the application.
	 *
	 * @return void
	 */
	public function map()
	{
		$this->loadRoutesFrom(app_path('Http/routes.php'));
	}

}
