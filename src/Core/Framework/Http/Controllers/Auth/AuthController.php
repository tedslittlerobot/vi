<?php namespace Vi\Core\Framework\Http\Controllers\Auth;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Routing\Redirector;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite as Socialize;

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
	 * @Get("i/am/important", as="login.process")
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
				'email' => trans( 'vi::validation.login' ),
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

	// ! Password Reset
	// @todo - actually implement password reset

	// ! Social Login
	// @todo - move to separate class / trait?

	/**
	 * The enabled social providers
	 *
	 * @todo  - move to separate class / config?
	 * @todo  - allow specifying scopes on each provider
	 *
	 * @var array
	 */
	protected $socialProviders = [];

	/**
	 * @Get("log/me/in/socially/with/{social_provider}")
	 *
	 * @todo - route bind social_provider
	 */
	public function redirectToProvider( $provider )
	{
		if ( !in_array($provider, $this->socialProviders) )
			abort(404, "No Such Provider");

		return Socialize::with( $provider )
			->scopes([])
			->redirect();
	}

	/**
	 * @Get("log/me/in/now/through/{social_provider}")
	 */
	public function handleProviderCallback( $provider )
	{
		$user = Socialize::with( $provider )->user();

		// @todo - map user to db, add tokens, save, login, redirect to login url (admin?)
		abort(501, "Social Login Not Finished");
	}

}
