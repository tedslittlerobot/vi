<?php namespace Vi\Core\Users\Permissions;

trait UserWithGroupsPermissionsTrait {

	/**
	 * Get all the user's permissions
	 *
	 * @return array
	 */
	public function allPermissions()
	{
		$userPermissions = $this->userPermissions();

		$groupPermissions = array_flatten( $this->groups->lists('permissions') );

		return array_unique( array_merge($userPermissions, $groupPermissions) );
	}

}
