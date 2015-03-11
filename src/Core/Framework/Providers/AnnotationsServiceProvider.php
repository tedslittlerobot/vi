<?php namespace Vi\Core\Framework\Providers;

use Collective\Annotations\AnnotationsServiceProvider as ServiceProvider;

class AnnotationsServiceProvider extends ServiceProvider {

	/**
	 * The classes to scan for event annotations.
	 *
	 * @var array
	 */
	protected $scanEvents = [];

	/**
	 * The classes to scan for route annotations.
	 *
	 * @var array
	 */
	protected $scanRoutes = [];

	/**
	 * Determines if we will auto-scan in the local environment.
	 *
	 * @var bool
	 */
	protected $scanWhenLocal = true;

	/**
	 * Scan the controllers directory
	 *
	 * @var boolean
	 */
	protected $scanControllers = true;

	/**
	 * @{inheritdoc}
	 */
	public function routeScans() {
		$classes = parent::routeScans();

		$viClasses = $this->app->make('Illuminate\Filesystem\ClassFinder')
			->findClasses( realpath(__DIR__ . '/../Http/Controllers') );

		$classes = array_merge( $classes, $viClasses );

		return $classes;
	}

}
