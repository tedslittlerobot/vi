<?php namespace MyTesco\Users\Permissions;

use Illuminate\Database\Eloquent\Builder;

trait PermissionsTrait {

	use PermissionsGettersAndSettersTrait;

	/**
	 * The groups the user is in
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function groups()
	{
		return $this->belongsToMany( UserGroup::class );
	}

	/**
	 * Determine if the user is a ninja
	 *
	 * @return bool
	 */
	public function isNinja()
	{
		return $this->is_ninja;
	}

	// ! getters and setters - user level

	/**
	 * Get all the permissons for a user
	 *
	 * Pass true as an argument to show the group structure. The results will be
	 * an array with the 0'th item being an array of the user's specific
	 * permissions. The other items will be key => value pairs of the
	 * group id => group's permissions
	 *
	 * @param  bool $details
	 * @return array
	 */
	public function allPermissions( $details = false )
	{
		$permissions = [
			$this->currentPermissions()
		];

		foreach ($this->groups as $group)
		{
			$permissions[$group->id] = $group->permissions;
		}

		return $details ? $permissions : array_unique(array_flatten($permissions));
	}

	/**
	 * Check if all of the given permissions are present
	 *
	 * @param  array|string  $permissionsToCheck
	 * @param  boolean       $respectNinja
	 * @return bool
	 */
	public function can( $permissionsToCheck, $respectNinja = true )
	{
		if ( $respectNinja && $this->isNinja() ) return true;

		$permissionsToCheck = (array)$permissionsToCheck;

		$matchedPermissions = array_intersect($permissionsToCheck, $this->allPermissions());

		return $matchedPermissions == $permissionsToCheck;
	}

	/**
	 * Check if any of the given permissions are present
	 *
	 * @param  array|string  $permissions
	 * @param  bool       $respectNinja
	 * @return bool
	 */
	public function canDoAny( $permissionsToCheck, $respectNinja = true )
	{
		if ( $respectNinja && $this->isNinja() ) return true;

		$permissionsToCheck = (array)$permissionsToCheck;

		$matchedPermissions = array_intersect($permissionsToCheck, $this->allPermissions());

		return count( $matchedPermissions ) > 0;
	}

	// ! Query Scopes

	/**
	 * Apply a query scope to only show ninja
	 *
	 * @param  Builder $query
	 * @param  string  $boolean
	 * @return Builder
	 */
	public function scopeWhereNinja(Builder $query, $boolean = 'and')
	{
		return $query->where( 'is_ninja', '=', true, $boolean );
	}

	/**
	 * Apply a query scope to only show non-ninja
	 *
	 * @param  Builder $query
	 * @param  string  $boolean
	 * @return Builder
	 */
	public function scopeWhereNotNinja(Builder $query, $boolean = 'and')
	{
		return $query->where( 'is_ninja', '!=', true, $boolean );
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
		$permissionsQuery = function($query) use ($permissions)
		{
			foreach ((array) $permissions as $permission)
			{
				$query->whereNested(
					$this->generateSinglePermissionCheckQueryClosure($permission)
				);
			}
		};

		// either wrap the query in an or where ninja, or apply it raw
		if ( ! $respectNinja )
			return $query->whereNested($permissionsQuery);

		return $query->whereNested(function($query) use ($permissionsQuery)
		{
			$query->where( 'is_ninja', '=', true, 'and' );

			$query->whereNested( $permissionsQuery, 'or' );
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
	 * @param  bool                                      $respectNinja
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function scopeWhereCanDoAny(Builder $query, $permissions, $respectNinja = true)
	{
		if ( empty($permissions) ) return;

		// a query closure for the loose permissions check
		$permissionsQuery = function($query) use ($permissions)
		{
			foreach ((array) $permissions as $permission)
			{
				$query->whereNested(
					$this->generateSinglePermissionCheckQueryClosure($permission),
					'or'
				);
			}
		};

		// either wrap the query in an or where ninja, or apply it raw
		if ( ! $respectNinja )
			return $query->whereNested($permissionsQuery);

		return $query->whereNested(function($query) use ($permissionsQuery)
		{
			$query->where( 'is_ninja', '=', true, 'and' );

			$query->whereNested( $permissionsQuery, 'or' );
		});
	}

	/**
	 * Generate a query closure to check against a single permission
	 *
	 * This checks the users table, and the groups relationship
	 *
	 * @param  string $permission
	 * @return Closure
	 */
	protected function generateSinglePermissionCheckQueryClosure( $permission )
	{
		return function($query) use ($permission)
		{
			$query->orWhere( $this->getPermissionsKey(), 'LIKE', "%\"{$permission}\"%" );

			$eloquery = new Builder($query);

			$eloquery->setModel($this);

			$eloquery->orWhereHas('groups', function($query) use ($permission)
			{
				$query->where( $this->getPermissionsKey(), 'LIKE', "%\"{$permission}\"%" );
			});
		};
	}

}
