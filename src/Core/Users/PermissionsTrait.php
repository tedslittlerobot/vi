<?php namespace Vi\Core\Users;

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
	public function setPermissionsAttribute( $permissions, $allowAny = false )
	{
		$permissions = $this->permissionsIntersectAllowed(
			(array) $permissions, $allowAny
		);

		natsort($permissions);

		$this->attributes[ $this->getPermissionsKey() ] = json_encode(
			array_values( array_unique($permissions) )
		);

		return $this;
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
	public function can( $checkPermissions, $respectNinja = true )
	{
		if ( $respectNinja && $this->isNinja() ) return true;

		$checkPermissions = is_array($checkPermissions) ? $checkPermissions : func_get_args();

		$matchedPermissions = array_intersect($checkPermissions, $this->currentPermissions());

		return $matchedPermissions == $checkPermissions;
	}

	/**
	 * Check if any of the given permissions are present
	 *
	 * @param  array|string $permissions
	 * @return boolean
	 */
	public function canDoAny( $checkPermissions, $respectNinja = true )
	{
		if ( $respectNinja && $this->isNinja() ) return true;

		$checkPermissions = is_array($checkPermissions) ? $checkPermissions : func_get_args();

		$matchedPermissions = array_intersect($checkPermissions, $this->currentPermissions());

		return count( $matchedPermissions ) > 0;
	}

	// ! Modifiers

	/**
	 * Grant the given permissions to the model
	 *
	 * @param  array|string $permissions
	 * @return array
	 */
	public function grant( $grantPermissions )
	{
		$grantPermissions = $this->permissionsIntersectAllowed(
			is_array($grantPermissions) ? $grantPermissions : func_get_args()
		);

		$this->setPermissionsAttribute(
			array_merge($this->currentPermissions(), $grantPermissions)
		);

		return $this;
	}

	/**
	 * Grant the ninja permission to the model
	 *
	 * @param  array|string $permissions
	 * @return array
	 */
	public function grantNinja()
	{
		$permissions = $this->currentPermissions();

		$permissions[] = $this->getPermissionsNinjaKey();

		$this->setPermissionsAttribute( $permissions, true );

		return $this;
	}

	/**
	 * Deny the given permissions from the model
	 *
	 * @param  array|string $permissionsToDeny
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

	// ! Query Scopes

	/**
	 * Apply a query scope to only show ninja
	 *
	 * @param  Builder $query
	 * @return Builder
	 */
	public function scopeWhereNinja(Builder $query)
	{
		return $query->where( $this->getPermissionsKey(), 'LIKE', "%\"{$this->getPermissionsNinjaKey()}\"%" );
	}

	/**
	 * Apply a query scope to only show non-ninja
	 *
	 * @param  Builder $query
	 * @return Builder
	 */
	public function scopeWhereNotNinja(Builder $query)
	{
		return $query->where( $this->getPermissionsKey(), 'NOT LIKE', "%\"{$this->getPermissionsNinjaKey()}\"%" );
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

		return $this->whereNestedOrNinja( $permissionsQuery, $respectNinja );
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

		return $query->whereNestedOrNinja( $permissionsQuery, $respectNinja );
	}

	/**
	 * Either wrap the given query closure to allow ninja autonomy, or apply
	 * the nested query raw
	 *
	 * @param  Builder  $query
	 * @param  Closure  $closure
	 * @param  boolean  $respectNinja
	 * @return Builder
	 */
	public function scopeWhereNestedOrNinja( Builder $query, Closure $closure, $respectNinja = true )
	{
		// if ignoring ninjas, just apply the query
		if ( ! $respectNinja )
			return $query->whereNested( $closure );

		// otherwise wrap it in a query to match ninjas
		return $query->whereNested(function($query) use ( $closure )
		{
			$query
				->whereNinja()
				->whereNested( $closure, 'or' );
		});
	}

	// ! Helper methods

	/**
	 * Filter a list of permissions against the allowed permissions
	 *
	 * @param  array   $permissions
	 * @param  boolean $allowAny
	 * @return array
	 */
	public function permissionsIntersectAllowed( array $permissions, $allowAny = false )
	{
		if ($allowAny) return $permissions;

		return array_intersect( $permissions, $this->availablePermissions() );
	}

	/**
	 * Get an array of permissions for a select box
	 *
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
