<?php namespace Vi\Core\Auth;

use Vi\Core\Users\User;

class PasswordReseter {

	public function requestPasswordReset( array $input )
	{
		// @todo - send reset email
	}

	public function checkPasswordResetToken( $token )
	{
		// @todo - check reset token
	}

	public function resetPassword( $token, array $input )
	{
		// @todo actually reset the password
	}

}
