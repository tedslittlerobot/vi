<?php namespace Vi\Http\Middleware;

use Closure;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Request;

class DatabaseTransactionWrapper implements Middleware {

	/**
	 * The database manager instance
	 *
	 * @var \Illuminate\Database\DatabaseManager
	 */
	protected $db;

	/**
	 * The config repository instance
	 *
	 * @var \Illuminate\Contracts\Config\Repository
	 */
	protected $config;

	public function __construct( Config $config, DatabaseManager $db )
	{
		$this->config = $config;
		$this->db = $db;
	}

	/**
	 * @{inheritdoc}
	 */
	public function handle($request, Closure $next)
	{
		// @todo - allow config overriding
		if ( false || $this->isReading($request) )
			return $next($request);

		$response = $this->db->transaction(function() use ($next, $request)
		{
			return $next($request);
		});

		return $response;
	}

	/**
	 * Determine if the HTTP request uses a ‘read’ verb.
	 *
	 * Stolen from Illuminate\Foundation\Http\Middleware\VerifyCsrfToken
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return bool
	 */
	protected function isReading( Request $request )
	{
		return in_array($request->method(), ['HEAD', 'GET', 'OPTIONS']);
	}

}
