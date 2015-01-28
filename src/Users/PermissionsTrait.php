<?php namespace Vi\Users;

use Illuminate\Database\Eloquent\Builder;

trait PermissionsTrait {

	// ! Getters and Setters

	/**
	 * Get the available permissions for/to the model
	 *
	 * @return array
	 */
	public function availablePermissions()
	{
		return [];
	}

	/**
	 * Get the key used to store permissions
	 *
	 * @return string
	 */
	public function getPermissionsKey()
	{
		return 'permissions';
	}

	/**
	 * Get the key to use for permission ninja-ing
	 *
	 * @return string
	 */
	protected function getPermissionsNinjaKey()
	{
		return 'ninja';
	}

	/**
	 * Get the current permissions of the model
	 *
	 * @return array
	 */
	public function currentPermissions()
	{
		return json_decode( $this->attributes[ $this->getPermissionsKey() ] );
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
		$finalPermissions = [];

		foreach ( (array) $permissions as $permission )
		{
			if ( in_array($permission, $this->availablePermissions()) )
				$finalPermissions[] = $permission;
		}

		natsort($finalPermissions);

		$this->attributes[ $this->getPermissionsKey() ] = json_encode(
			array_values( array_unique($finalPermissions) )
		);
	}

	// ! Comparators

	/**
	 * Determine if the current model is a ninja
	 *
	 * @return boolean
	 */
	public function isNinja()
	{
		return in_array( $this->getPermissionsNinjaKey(), $this->currentPermissions() );
	}


	/**
	 * Check if all of the given permissions are present
	 *
	 * @param  array|string $permissions
	 * @return boolean
	 */
	public function can( $checkPermissions )
	{
		if ( $this->isNinja() ) return true;

		$checkPermissions = is_array($checkPermissions) ? $checkPermissions : func_get_args();

		$currentPermissions = $this->currentPermissions();

		foreach ($checkPermissions as $permission)
		{
			if ( ! in_array($permission, $currentPermissions) )
				return false;
		}

		return true;
	}

	/**
	 * Check if any of the given permissions are present
	 * @param  array|string $permissions
	 * @return boolean
	 */
	public function canDoAny( $checkPermissions )
	{
		if ( $this->isNinja() ) return true;

		$checkPermissions = is_array($checkPermissions) ? $checkPermissions : func_get_args();

		$currentPermissions = $this->currentPermissions();

		foreach ($checkPermissions as $permission)
		{
			if ( in_array($permission, $currentPermissions) )
				return true;
		}

		return false;
	}

	// ! Modifiers

	/**
	 * Grant the given permissions to the model
	 * @param  array|string $permissions
	 * @return array
	 */
	public function grant( $grantPermissions )
	{
		// parse input to array
		$grantPermissions = is_array($grantPermissions) ? $grantPermissions : func_get_args();

		$currentPermissions = $this->currentPermissions();

		foreach ($grantPermissions as $permission)
		{
			$currentPermissions[] = $permission;
		}

		return $this->setPermissionsAttribute( $currentPermissions );
	}

	/**
	 * Deny the given permissions from the model
	 * @param  array|string $permissionsToDeny
	 * @return array
	 */
	public function deny( $denyPermissions )
	{
		$denyPermissions = is_array($denyPermissions) ? $denyPermissions : func_get_args();

		$currentPermissions = $this->currentPermissions();

		foreach ($denyPermissions as $permission)
		{
			// remove it if it exists
			if (($index = array_search($permission, $currentPermissions)) !== false)
				array_splice($currentPermissions, $index, 1);
		}

		return $this->setPermissionsAttribute( $currentPermissions );
	}

	// ! Query Scopes

	/**
	 * Apply a query scope to only show ninja
	 *
	 * @param  Builder $query
	 * @return Builder
	 */
	public function scopeWhereNinja(Builder $query)
	{
		return $query->where( $this->getPermissionsKey(), 'LIKE', "%\"{$permission}\"%" );
	}

	/**
	 * Apply a query scope to only show non-ninja
	 *
	 * @param  Builder $query
	 * @return Builder
	 */
	public function scopeWhereNotNinja(Builder $query)
	{
		return $query->where( $this->getPermissionsKey(), 'NOT LIKE', "%\"{$permission}\"%" );
	}

	/**
	 * Apply a where can (strict permissions check) query scope to the model.
	 * The first argument are the permissions to check against. The second is
	 * whether or not to respect ninja as an override (default true).
	 *
	 * If you wanted to check a user's permission, leave the $respectNinja
	 * argument blank to use the default (normal) functionality - ie. ninja will
	 * automatically pass
	 *
	 * If, for example, you wanted to show a list of users who can edit posts,
	 * but don't want to include ninja in that list, pass false as the last
	 * argument to the scope.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder     $query
	 * @param  array|string                              $permissions
	 * @param  bool                                      $respectNinja
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function scopeWhereCan(Builder $query, $permissions, $respectNinja = true)
	{
		// a query closure for the strict permissions check
		$permissionsQuery = function( Builder $query ) use ($permissions)
		{
			foreach ((array) $permissions as $permission)
			{
				$query->where( $this->getPermissionsKey(), 'LIKE', "%\"{$permission}\"%" );
			}
		};

		// either wrap the query in an or where ninja, or apply it raw
		if ( ! $respectNinja )
			return $query->whereNested($permissionsQuery);

		return $query->whereNested(function($query) use ($permissionsQuery)
		{
			$query->where( $this->getPermissionsKey(), 'LIKE', "%\"{$this->getPermissionsNinjaKey()}\"%" );

			$query->whereNested($permissionsQuery, 'or');
		});
	}

	/**
	 * Apply an 'or' style where can (non-strict permissions check) query scope
	 * to the model.
	 *
	 * If you wanted to check a user's permission, leave the $respectNinja
	 * argument blank to use the default (normal) functionality - ie. ninja will
	 * automatically pass
	 *
	 * If, for example, you wanted to show a list of users who can edit posts,
	 * but don't want to include ninja in that list, pass false as the last
	 * argument to the scope.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder     $query
	 * @param  array|string                              $permissions
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function scopeWhereCanDoAny(Builder $query, $permissions, $respectNinja = true)
	{
		$permissions = (array) $permissions; // convert to array

		if ( empty($permissions) ) return $query;

		// a query closure for the loose permissions check
		$permissionsQuery = function($query) use ($permissions)
		{
			$query->where( $this->getPermissionsKey(), 'LIKE', '%"' . array_shift($permissions) . '"%' );

			foreach ( $permissions as $permission )
				$query->orWhere( $this->getPermissionsKey(), 'LIKE', "%\"{$permission}\"%" );
		};

		// either wrap the query in an or where ninja, or apply it raw
		if ( ! $respectNinja )
			return $query->whereNested($permissionsQuery);

		return $query->whereNested(function($query) use ($permissionsQuery)
		{
			$query->where( $this->getPermissionsKey(), 'LIKE', "%\"{$this->getPermissionsNinjaKey()}\"%" );

			$query->whereNested( $permissionsQuery, 'or' );
		});
	}

	// ! Helper methods

	/**
	 * Get an array of permissions for a select box
	 * @return array
	 */
	public function availablePermissionsSelect()
	{
		$select = ['any' => 'Any'];

		foreach ($this->availablePermissions() as $key => $value)
		{
			if (is_string($key))
				$select[$key] = $value;
			else
				$select[$value] = ucwords(str_replace('-', ' ', $value));
		}

		return $select;
	}

}
