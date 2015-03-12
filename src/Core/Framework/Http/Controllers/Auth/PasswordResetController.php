<?php namespace Vi\Core\Framework\Http\Controllers\Auth;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Routing\Redirector;
use Illuminate\Http\Request;

use Vi\Core\Framework\Http\Controllers\Controller;

/**
 * @Middleware("guest")
 */
class PasswordResetController extends Controller {

	public function __construct( Guard $guard )
	{
		$this->guard = $guard;
	}

	// ! Request Password Reset Form

	/**
	 * @Get("i/forgot/my/password", as="auth.password-reset.request")
	 */
	public function showResetRequestForm()
	{
		return view('vi::auth.password-reset.request');
	}

	/**
	 * @Post("send/me/a/reset/link", as="auth.password-reset.request.process")
	 */
	public function sendResetEmail( Request $request )
	{
		// @todo - send reset email
		// @todo - flash notice
		return redirect( route('login') );
	}

	// ! Respond to emails

	/**
	 * @Get("here/is/my/code/{reset_token}/reset/my/password", as="auth.password-reset.reset")
	 */
	public function showResetRequestForm( $token )
	{
		return view('vi::auth.password-reset.reset');
	}

	/**
	 * @Put("here/is/my/code/{reset_token}/reset/my/password", as="auth.password-reset.reset.process")
	 */
	public function ( Request $request )
	{
		// @todo actually reset the password

		return redirect( route('login') );
	}

}
