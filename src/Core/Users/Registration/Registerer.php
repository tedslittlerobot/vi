<?php namespace Vi\Core\Users\Registration;

use Vi\Core\Auth\Registration\RegistererInterface;
use Vi\Core\Auth\Registration\SendsConfirmationEmail;
use Vi\Core\Users\User;

/**
 * @todo bind to registerer interface
 */
class Registerer implements RegistererInterface, SendsConfirmationEmail {

	public function __construct()
	{
		//
	}

	// ! Registration

	/**
	 * @{inheritdoc}
	 */
	public function validationRules( array $input )
	{
		return [
			'firstname' => 'required|max:255',
			'lastname' => 'required|max:255',
			'nickname' => 'max:255',
			'email' => 'required|email|max:255|unique:users',
			'password' => 'required|confirmed|min:6',
		];
	}

	/**
	 * @{inheritdoc}
	 */
	public function register( array $input )
	{
		// @todo - register a user
		// @todo - send a confirm email email
	}

	// ! Confirmation Email

	/**
	 * @{inheritdoc}
	 */
	public function sendConfirmEmail( User $user )
	{
		// @todo - send registration confirm email
	}

	/**
	 * @{inheritdoc}
	 */
	public function confirm( $code, array $input )
	{
		// @todo check confirm code
		// @todo confirm / approve user
	}

}
