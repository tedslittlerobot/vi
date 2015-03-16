<?php namespace Vi\Core\Auth\Registration;

use Vi\Core\Users\User;

interface SendsConfirmationEmail {

	/**
	 * Send the user a confirmation email
	 *
	 * @param  User   $user
	 * @return void
	 */
	public function sendConfirmEmail( User $user );

	/**
	 * Confirm the user
	 *
	 * @param  string $code
	 * @param  array  $input
	 * @return void
	 */
	public function confirm( $code, array $input );

}
