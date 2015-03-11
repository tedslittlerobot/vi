<?php namespace Vi\Core\Framework\Http\Controllers\Auth;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Laravel\Socialite\Contracts\Provider as SocialiteProvider;

use Vi\Core\Framework\Http\Controllers\Controller;

/**
 * @Middleware("guest", except="logout")
 */
class SSOController extends Controller {

	public function __construct( Guard $guard )
	{
		$this->guard = $guard;
	}

	/**
	 * @Get("log/me/in/socially/with/{social_provider}")
	 */
	public function redirectToProvider( $socialite )
	{
		return $socialite->redirect();
	}

	/**
	 * @Get("social/login/callback/for/{social_provider}")
	 */
	public function handleProviderCallback( $socialite )
	{
		$socialUser = $socialite->user();

		// @todo - map user to db, add tokens, save, login, redirect to login url (admin?)
		abort(501, "Social Login Not Finished");

		$this->guard->login( $user );

		return route('admin');
	}

}
