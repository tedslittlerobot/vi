<?php namespace Vi\Core\Users\Auth;

use Vi\Core\Users\User;

interface RegistererInterface {

	/**
	 * The validation rules for registration
	 *
	 * @param  array  $input
	 * @return array
	 */
	public function validationRules( array $input );

	/**
	 * Register a new user
	 *
	 * @param  array  $input
	 * @return \Vi\Core\Users\User
	 */
	public function register( array $input );

}
