<?php namespace Vi\Notices\Middleware;

use Closure;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Contracts\Support\MessageBag as MessageBagContract;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\MessageBag;

class ShareNoticesFromSession implements Middleware {

	/**
	 * The view factory implementation.
	 *
	 * @var \Illuminate\Contracts\View\Factory
	 */
	protected $view;

	/**
	 * Create a new notice binder instance.
	 *
	 * @param  \Illuminate\Contracts\View\Factory  $view
	 * @return void
	 */
	public function __construct(ViewFactory $view)
	{
		$this->view = $view;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		// If the current session has an "notices" variable bound to it, we will share
		// its value with all view instances so the views can easily access notices
		// without having to bind. An empty bag is set when there aren't notices.
		$notices = $request->session()->get('notices');

		if ( ! $notices instanceof MessageBagContract )
		{
			$notices = new MessageBag( (array) $notices );
		}

		$this->view->share( 'notices', $notices );

		return $next($request);
	}

}
