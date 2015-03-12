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

	/**
	 * @Get("i/want/in", as="register")
	 */
	public function registrationForm()
	{
		return view('vi::auth.registration.register');
	}

	/**
	 * @Post("register/me/now", as="register.process")
	 */
	public function register()
	{
		// @todo - if registered and unconfirmed / unapproved
		if(false)
		{
			return redirect( route( 'register.processed' ) );
		}

		// @todo - do registration - request, repo, etc.
		// @todo set registered data in session

		return redirect( route( 'register.processed' ) );
	}

	/**
	 * @Get("still/not/finished")
	 *
	 * @todo limit access by session
	 */
	public function processed()
	{
		return view('vi::auth.registration.registered');
	}

	/**
	 * @Post("i/lost/that/email/you/sent")
	 */
	public function resendConfirmEmail()
	{
		// @todo resend registration confirmation
		// @todo flash notice
	}

	/**
	 * @Get("i/clicked/the/confirm/email/{confirm_code}")
	 */
	public function confirm()
	{
		// @todo check confirm code
		// @todo confirm / approve user
		return redirect( route('admin') );
	}

}
