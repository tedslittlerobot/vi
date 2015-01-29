<?php namespace Vi\Users;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements AuthenticatableContract {

	use Authenticatable, PermissionsTrait, UserTrait;

	/**
	 * The available permissions for the model
	 * @var array
	 */
	public function availablePermissions()
	{
		return [];
	}

}
