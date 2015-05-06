<?php namespace Vi\Core\Users\Auth;

use Vi\Core\Users\User;

class PasswordResetter {

	/**
	 * Send an email with a password reset link
	 *
	 * @param  array  $input
	 * @return void
	 */
	public function requestPasswordReset( array $input )
	{
		// @todo - send reset email
	}

	/**
	 * Check the given password reset token is valid
	 *
	 * @param  string $token
	 * @return bool
	 */
	public function checkPasswordResetToken( $token )
	{
		// @todo - check reset token
	}

	/**
	 * The validation rules for new passwords
	 *
	 * @param  array  $input
	 * @return array
	 */
	public function passwordResetRules( array $input )
	{
		return [
			'password' => 'required|confirmed|min:6',
		];
	}

	/**
	 * Reset the given user's password
	 *
	 * @param  string $token
	 * @param  array  $input
	 * @return \Vi\Core\Users\User
	 */
	public function resetPassword( $token, array $input )
	{
		// @todo actually reset the password
	}

}
