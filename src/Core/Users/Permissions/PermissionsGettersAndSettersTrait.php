<?php namespace Vi\Core\Users\Permissions;

trait PermissionsGettersAndSettersTrait {

	/**
	 * Get the key used to store permissions
	 *
	 * @return string
	 */
	public function getPermissionsKey()
	{
		return 'permissions';
	}

	// ! getters and setters - user level

	/**
	 * Get the current permissions of the model
	 *
	 * @return array
	 */
	public function currentPermissions()
	{
		// the options on the json_decode are to ensure array output format, and
		// ensure a single level array is output It does this quite strictly -
		// ie. if there is any depth, this method will return an empty array
		return (array) json_decode( $this->attributes[ $this->getPermissionsKey() ], true, 2 );
	}

	/**
	 * Convert the permissions to an array
	 *
	 * @return array
	 */
	public function getPermissionsAttribute()
	{
		return $this->currentPermissions();
	}

	/**
	 * Convert the given permissions to a json string for storage
	 *
	 * @param array|string $permissions
	 */
	public function setPermissionsAttribute( $permissions )
	{
		natsort($permissions);

		$this->attributes[ $this->getPermissionsKey() ] = json_encode(
			array_values( array_unique($permissions) )
		);
	}

	/**
	 * Grant the given permissions to the model
	 *
	 * @param  array|string $permissions...
	 * @return array
	 */
	public function grant( $grantPermissions )
	{
		$grantPermissions = is_array($grantPermissions) ? $grantPermissions : func_get_args();

		$this->setPermissionsAttribute(
			array_merge($this->currentPermissions(), $grantPermissions)
		);

		return $this;
	}

	/**
	 * Deny the given permissions from the model
	 *
	 * @param  array|string $permissions...
	 * @return array
	 */
	public function deny( $denyPermissions )
	{
		$denyPermissions = is_array($denyPermissions) ? $denyPermissions : func_get_args();

		$this->setPermissionsAttribute(
			array_diff($this->currentPermissions(), $denyPermissions)
		);

		return $this;
	}
}
