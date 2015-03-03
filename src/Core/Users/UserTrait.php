<?php namespace Vi\Core\Users;

trait UserTrait {

	/**
	 * Capitalise the first name
	 *
	 * @param string $firstname
	 */
	public function setFirstnameAttribute( $firstname )
	{
		$this->attributes['firstname'] = ucwords($firstname);
	}

	/**
	 * Captialise the last name
	 *
	 * @param string $lastname
	 */
	public function setLastnameAttribute( $lastname )
	{
		$this->attributes['lastname'] = ucwords($lastname);
	}

	/**
	 * Get the user's full name
	 *
	 * @return string
	 */
	public function getFullnameAttribute()
	{
		return "{$this->getFirstnameAttribute()} {$this->getLastnameAttribute()}";
	}

	/**
	 * Get the user's nickname or real name
	 *
	 * @return string
	 */
	public function getNameAttribute()
	{
		return $this->getNicknameAttribute() ?: $this->getFullnameAttribute();
	}

}
