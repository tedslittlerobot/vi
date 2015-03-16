<?php namespace Vi\Core\Framework\Http\Controllers\Auth;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Routing\Redirector;
use Illuminate\Http\Request;

use Vi\Core\Auth\PasswordResetter;
use Vi\Core\Framework\Http\Controllers\Controller;

/**
 * @Middleware("guest")
 */
class PasswordResetController extends Controller {

	public function __construct( PasswordResetter $resetter )
	{
		$this->resetter = $resetter;
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
		$this->resetter->requestPasswordReset( $request->input() );

		// @todo - flash success notice

		return redirect( route('login') );
	}

	// ! Respond to emails

	/**
	 * @Get("here/is/my/code/{reset_token}/reset/my/password", as="auth.password-reset.reset")
	 */
	public function showPasswordResetForm( $token )
	{
		if ( ! $this->resetter->tokenIsValid( $token ) )
		{
			abort(404);
		}

		return view('vi::auth.password-reset.reset');
	}

	/**
	 * @Put("here/is/my/code/{reset_token}/reset/my/password", as="auth.password-reset.reset.process")
	 *
	 * @todo add request
	 */
	public function resetPassword( Request $request )
	{
		$this->resetter->resetPassword( $request->input() );

		// @todo flash success notice

		return redirect( route('login') );
	}

}
