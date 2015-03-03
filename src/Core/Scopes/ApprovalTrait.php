<?php namespace Vi\Core\Scopes;

use Carbon\Carbon;

trait ApprovalTrait {

	/**
	 * Boot the approval trait for a model.
	 *
	 * @return void
	 */
	public static function bootApprovalTrait()
	{
		static::addGlobalScope( static::makeApprovalScope() );
	}

	// ! Actions and Methods

	/**
	 * Approve the model.
	 *
	 * @return void
	 */
	public function approve()
	{
		$query = $this->newQuery()->withUnapproved()->where($this->getKeyName(), $this->getKey());

		$this->{$this->getApprovedAtColumn()} = $time = $this->freshTimestamp();

		return $query->update(array($this->getApprovedAtColumn() => $this->fromDateTime($time)));
	}

	/**
	 * Reject the model.
	 *
	 * @return void
	 */
	public function reject()
	{
		$query = $this->newQuery()->withUnapproved()->where($this->getKeyName(), $this->getKey());

		$this->attributes[$this->getApprovedAtColumn()] = $time = static::getRejectionFormat();

		return $query->update(array($this->getApprovedAtColumn() => $time));
	}

	// ! Accessors and Mutators

	/**
	 * Get the status of the model
	 * @return  string
	 */
	public function getApprovalStatusAttribute()
	{
		if ( $this->isPendingApproval() )
			return 'pending';

		return $this->isApproved() ? 'approved' : 'rejected';
	}

	/**
	 * Show a different value depending on the status of the model
	 * @param  mixed $approved
	 * @param  mixed $pending
	 * @param  mixed $rejected
	 * @return mixed
	 */
	public function approvalSwitch( $approved, $pending, $rejected )
	{
		$status = $this->getApprovalStatusAttribute();

		if ($status == 'approved')
			return is_callable($approved) ? call_user_func_array($approved, [$this]) : $approved;

		if ($status == 'pending')
			return is_callable($pending) ? call_user_func_array($pending, [$this]) : $pending;

		if ($status == 'rejected')
			return is_callable($rejected) ? call_user_func_array($rejected, [$this]) : $rejected;
	}

	// ! Comparators

	/**
	 * Determine if the model instance has been approved.
	 *
	 * @return bool
	 */
	public function isApproved()
	{
		if ( $this->isPendingApproval() || $this->isRejected() ) return false;

		return $this->{$this->getApprovedAtColumn()}->isPast() || $this->{$this->getApprovedAtColumn()}->eq(\Carbon\Carbon::now());
	}

	/**
	 * Determine if the model instance has been considered.
	 *
	 * @return boolean
	 */
	public function isPendingApproval()
	{
		return is_null($this->attributes[ $this->getApprovedAtColumn() ]);
	}

	/**
	 * Determine if the model instance has been rejected.
	 *
	 * @return boolean
	 */
	public function isRejected()
	{
		return $this->attributes[ $this->getApprovedAtColumn() ] == static::getRejectionFormat();
	}

	// ! Helpers

	/**
	 * Get the name of the "deleted at" column.
	 *
	 * @return string
	 */
	public function getApprovedAtColumn()
	{
		return defined('static::APPROVED_AT') ? static::APPROVED_AT : 'approved_at';
	}

	/**
	 * Get the fully qualified "deleted at" column.
	 *
	 * @return string
	 */
	public function getQualifiedApprovedAtColumn()
	{
		return $this->getTable().'.'.$this->getApprovedAtColumn();
	}

	/**
	 * Get the attributes that should be converted to dates.
	 *
	 * @return array
	 */
	public function getDates()
	{
		return array_merge(parent::getDates(), [$this->getApprovedAtColumn()]);
	}

	/**
	 * Get the rejection format
	 *
	 * @return string
	 */
	public static function getRejectionFormat()
	{
		return '0000-00-00 00:00:00';
	}

	/**
	 * Create an approval query scope object
	 *
	 * @return ApprovedScope
	 */
	public static function makeApprovalScope()
	{
		return new ApprovalScope( static::getRejectionFormat() );
	}
}
