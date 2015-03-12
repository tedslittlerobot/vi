<?php namespace Vi\Core\Framework\Http\Controllers\Auth;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Routing\Redirector;
use Illuminate\Http\Request;

use Vi\Core\Framework\Http\Controllers\Controller;

/**
 * @Middleware("guest", except="logout")
 */
class AuthController extends Controller {

	public function __construct( Guard $guard )
	{
		$this->guard = $guard;
	}

	// ! Login / Logout

	/**
	 * @Get("log/me/in", as="login")
	 */
	public function showLoginForm()
	{
		return view('vi::auth.login');
	}

	/**
	 * @Post("i/am/important", as="login.process")
	 */
	public function processLogin( Request $request )
	{
		$this->validate($request, [
			'email' => 'required|email', 'password' => 'required',
		]);

		$credentials = $request->only('email', 'password');

		if ( $this->guard->attempt($credentials, $request->has('remember')) )
		{
			return redirect()->intended('/');
			// return redirect()->intended( route('home') );
		}

		return redirect( route('login') )
			->withInput( $request->only('email', 'remember') )
			->withErrors([
				'login' => trans( 'vi::validation.login' ),
			]);
	}

	/**
	 * @Get("logout", as="logout")
	 */
	public function logout( Redirector $redirect )
	{
		$this->guard->logout();

		return redirect('/');
		// return redirect( route('home') ); // @todo - home route
	}

}
