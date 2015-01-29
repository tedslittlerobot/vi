<?php namespace Vi\Notifications;

use Illuminate\Database\Eloquent\Model;

use Vi\Users\User;

class Notification extends Model {

	/**
	 * The notification's user
	 *
	 * @return BelongsTo
	 */
	public function owner()
	{
		return $this->belongsTo( User::class, 'owner_id' );
	}

	/**
	 * The notification's user
	 *
	 * @return BelongsTo
	 */
	public function author()
	{
		return $this->belongsTo( User::class, 'author_id' );
	}

}
