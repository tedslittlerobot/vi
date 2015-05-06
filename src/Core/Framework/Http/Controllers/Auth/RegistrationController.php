<?php namespace Vi\Core\Framework\Http\Controllers\Auth;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Routing\Redirector;
use Illuminate\Http\Request;

use Vi\Core\Auth\Registration\RegistererInterface as Registerer;
use Vi\Core\Framework\Http\Controllers\Controller;
use Vi\Core\Users\Registration;

/**
 * @Middleware("guest")
 */
class RegistrationController extends Controller {

	public function __construct( Registerer $registerer )
	{
		$this->registerer = $registerer;
	}

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
	public function register(Request $request)
	{
		$user = $this->registerer->register( $request->input() );

		if ( $this->registerer instanceof SendsConfirmationEmail )
		{
			$this->registerer->sendConfirmationEmail( $user );
		}

		// @todo add notice

		return redirect( route( 'login' ) );
	}

	/**
	 * @Get("i/clicked/the/confirm/email/{confirm_code}")
	 */
	public function confirm( $code, Request $request )
	{
		if ( ! $this->registerer instanceof SendsConfirmationEmail )
		{
			abort(404);
		}

		$this->registerer->confirm( $code, $request->input() );

		return redirect( route('admin') );
	}

}
