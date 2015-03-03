<?php namespace Vi\Core\Scopes;

trait PublishingTrait {

	/**
	 * Boot the publishing trait for a model.
	 *
	 * @return void
	 */
	public static function bootPublishingTrait()
	{
		static::addGlobalScope( new PublishingScope );
	}

	// ! Actions and Methods

	/**
	 * Publish the model.
	 *
	 * @return void
	 */
	public function publish()
	{
		$query = $this->newQuery()->where($this->getKeyName(), $this->getKey());

		$this->{$this->getPublishedAtColumn()} = $time = $this->freshTimestamp();

		return $query->update(array($this->getPublishedAtColumn() => $this->fromDateTime($time)));
	}

	/**
	 * Unpublish the model.
	 *
	 * @return void
	 */
	public function unpublish()
	{
		$query = $this->newQuery()->where($this->getKeyName(), $this->getKey());

		$this->{$this->getPublishedAtColumn()} = null;

		return $query->update(array($this->getPublishedAtColumn() => null));
	}

	// ! Accessors and Mutators

	public function getIsPublishedAttribute()
	{
		return $this->isPublished();
	}

	// ! Comparators

	/**
	 * Determine if the model instance has been published.
	 *
	 * @return bool
	 */
	public function isPublished()
	{
		$publishedAt = $this->{$this->getPublishedAtColumn()};

		if ( is_null($publishedAt) ) return false;

		return $publishedAt->isPast();
	}

	// ! Helpers

	/**
	 * Get the name of the "published at" column.
	 *
	 * @return string
	 */
	public function getPublishedAtColumn()
	{
		return defined('static::PUBLISHED_AT') ? static::PUBLISHED_AT : 'published_at';
	}

	/**
	 * Get the fully qualified "published at" column.
	 *
	 * @return string
	 */
	public function getQualifiedPublishedAtColumn()
	{
		return $this->getTable().'.'.$this->getPublishedAtColumn();
	}

	/**
	 * Get the attributes that should be converted to dates.
	 *
	 * @return array
	 */
	public function getDates()
	{
		return array_merge(parent::getDates(), [$this->getPublishedAtColumn()]);
	}
}
