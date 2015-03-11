<?php namespace Vi\Core\Framework\Http\Controllers\Auth;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Routing\Redirector;
use Illuminate\Http\Request;

use Vi\Core\Framework\Http\Controllers\Controller;

/**
 * @Middleware("guest")
 */
class RegistrationController extends Controller {

	public function __construct( Guard $guard )
	{
		$this->guard = $guard;
	}

	// @todo - user registration

}
