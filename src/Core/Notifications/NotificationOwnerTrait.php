<?php namespace Vi\Core\Notifications;

trait NotificationOwnerTrait {

	/**
	 * The model's notifications
	 *
	 * @return HasMany
	 */
	public function notifications()
	{
		return $this->hasMany( Notification::class, 'owner_id' );
	}

}
