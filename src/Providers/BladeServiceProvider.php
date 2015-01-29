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
			'Errors',
			'Notices',
			'Title',
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
	 * @return Closure
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

	/**
	 * Compile the errors tags
	 *
	 * @return Closure
	 */
	public function compileErrors()
	{
		/**
		 * @firsterror('key')
		 *   {{ $message }}
		 * @endfirsterror
		 *
		 * @errors('key')
		 *   <li> {{$message}} </li>
		 * @enderrors
		 */
		return function($view, $compiler)
		{
			// @firsterror('key')
			$view = preg_replace(
				$compiler->createMatcher('firsterror'),
				'$1<?php if($errors->has($2)): ; $message = $errors->first($2); ?>',
				$view
			);
			// @endfirsterror
			$view = preg_replace(
				$compiler->createPlainMatcher('endfirsterror'),
				'$1<?php endif; ?>',
				$view
			);
			// @errors('key')
			$view = preg_replace(
				$compiler->createMatcher('errors'),
				'$1<?php foreach($errors->get($2) as $message): ?>',
				$view
			);
			// @enderrors
			$view = preg_replace(
				$compiler->createPlainMatcher('enderrors'),
				'$1<?php endforeach; ?>',
				$view
			);
			return $view;
		};
	}

	/**
	 * Compile the notices tags
	 *
	 * @return Closure
	 */
	public function compileNotices()
	{
		/**
		 * @notice
		 *   {{ $message }}
		 * @endnotice
		 *
		 * @notices
		 *   <li> {{$message}} </li>
		 * @endnotices
		 */
		return function($view, $compiler)
		{
			// @notice
			$view = preg_replace(
				$compiler->createPlainMatcher('firstnotice'),
				'$1<?php if(count((array)Session::get("notices", array())) > 0): ; list($message) = (array)Session::get("notices", array()); ?>',
				$view
			);
			// @endnotice
			$view = preg_replace(
				$compiler->createPlainMatcher('endfirstnotice'),
				'$1<?php endif; ?>',
				$view
			);
			// @notices
			$view = preg_replace(
				$compiler->createPlainMatcher('notices'),
				'$1<?php foreach((array)Session::get("notices", array()) as $message): ?>',
				$view
			);
			// @endnotices
			$view = preg_replace(
				$compiler->createPlainMatcher('endnotices'),
				'$1<?php endforeach; ?>',
				$view
			);
			return $view;
		};
	}

	/**
	 * Compile the title tag
	 *
	 * @return Closure
	 */
	public function compileTitle()
	{
		return function($view, $compiler)
		{
			// @title('Default Title')
			$view = preg_replace(
				$compiler->createMatcher('title'),
				'$1<?php echo e(isset($title) ? $title : $2) ?>',
				$view
			);
			return $view;
		};
	}

}
