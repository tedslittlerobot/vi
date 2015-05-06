<?php namespace Vi\Core\Users\Permissions;

use Illuminate\Database\Eloquent\Model;

use Vi\Core\Users\User;

class UserGroup extends Model {

	use PermissionsGettersAndSettersTrait;

	/**
	 * The users in the group
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function users()
	{
		return $this->belongsToMany( User::class );
	}
}
