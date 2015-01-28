<?php namespace Vi\Scopes;

use Illuminate\Database\Query\Expression;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ScopeInterface;

class ApprovalScope implements ScopeInterface {

	/**
	 * Initialise the scope
	 * @param string $rejectFormat   the format for rejected data
	 */
	public function __construct( $rejectFormat = '0000-00-00 00:00:00' )
	{
		$this->rejected = $rejectFormat;
	}

	/**
	 * All of the extensions to be added to the builder.
	 *
	 * @var array
	 */
	protected $extensions = ['WhereApproved', 'WhereNotRejected', 'WithUnapproved', 'WhereUnapproved', 'WhereRejected', 'WherePending', 'Approve', 'Reject'];

	/**
	 * Apply the scope to a given Eloquent query builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @return void
	 */
	public function apply(Builder $builder, Model $model)
	{
		$builder->where( $model->getQualifiedApprovedAtColumn(), '<=', $this->now() )
			->where( $model->getQualifiedApprovedAtColumn(), '!=', $this->rejected );

		$this->extend($builder, $model);
	}

	/**
	 * Remove the scope from the given Eloquent query builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @return void
	 */
	public function remove(Builder $builder, Model $model)
	{
		$column = $model->getQualifiedApprovedAtColumn();

		$query = $builder->getQuery();

		// remove wheres from query

		foreach ((array) $query->wheres as $key => $where)
		{
			if ($this->isApprovalConstraint($where, $column))
			{
				unset($query->wheres[$key]);
			}
		}

		$query->wheres = array_values($query->wheres);

		// remove bindings from query

		$whereBindings = $query->getRawBindings()['where'];

		foreach ($whereBindings as $key => $binding)
		{
			if ($binding == $this->rejected)
			{
				unset( $whereBindings[$key] );
			}
		}

		$query->setBindings( array_values($whereBindings), 'where' );
	}

	/**
	 * Extend the query builder with the needed functions.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @return void
	 */
	public function extend(Builder $builder, Model $model)
	{
		foreach ($this->extensions as $extension)
		{
			$this->{"add{$extension}"}($builder, $model);
		}
	}

	/**
	 * Add the whereApproved extension to the builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @return void
	 */
	public function addWhereApproved( Builder $builder, Model $model )
	{
		$builder->macro('whereApproved', function(Builder $builder) use( $model )
		{
			$builder->where( $model->getQualifiedApprovedAtColumn(), '<=', $this->now() )
				->where( $model->getQualifiedApprovedAtColumn(), '!=', $this->rejected );

			return $builder;
		});
	}

	/**
	 * Add the whereApproved extension to the builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @return void
	 */
	public function addWhereNotRejected( Builder $builder, Model $model )
	{
		$builder->macro('whereNotRejected', function( Builder $builder ) use( $model )
		{
			$this->remove($builder, $model);

			$model = $model;

			$builder->whereNested(function($query) use ($model)
			{
				$query->whereNested(function($query) use ($model)
				{
					$query->where( $model->getQualifiedApprovedAtColumn(), '<=', $this->now() )
						->where( $model->getQualifiedApprovedAtColumn(), '!=', $this->rejected );
				});

				$query->orWhereNull( $model->getQualifiedApprovedAtColumn() );
			});

			return $builder;
		});
	}

	/**
	 * Add the withUnapproved extension to the builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @return void
	 */
	public function addWithUnapproved( Builder $builder, Model $model )
	{
		$builder->macro('withUnapproved', function(Builder $builder) use( $model )
		{
			$this->remove($builder, $model);

			return $builder;
		});
	}

	/**
	 * Add the whereUnapproved extension to the builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @return void
	 */
	public function addWhereUnapproved( Builder $builder, Model $model )
	{
		$builder->macro('whereUnapproved', function(Builder $builder) use( $model )
		{
			$this->remove($builder, $model);

			$builder->whereNested(function($query) use ( $model )
			{
				$query->whereNull( $model->getQualifiedApprovedAtColumn() )
					->orWhere( $model->getQualifiedApprovedAtColumn(), $this->rejected );
			});

			return $builder;
		});
	}

	/**
	 * Add the whereRejected extension to the builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @return void
	 */
	public function addWhereRejected( Builder $builder, Model $model )
	{
		$builder->macro('whereRejected', function(Builder $builder) use( $model )
		{
			$this->remove($builder, $model);

			$builder->where( $model->getQualifiedApprovedAtColumn(), $this->rejected );

			return $builder;
		});
	}

	/**
	 * Add the wherePending extension to the builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @return void
	 */
	public function addWherePending( Builder $builder, Model $model )
	{
		$builder->macro('wherePending', function(Builder $builder) use( $model )
		{
			$this->remove($builder, $model);

			$builder->whereNull( $model->getQualifiedApprovedAtColumn() );

			return $builder;
		});
	}

	/**
	 * Add the approve extension to the builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @return void
	 */
	public function addApprove( Builder $builder, Model $model )
	{
		$builder->macro('approve', function(Builder $builder) use( $model )
		{
			$builder->whereUnapproved();

			return $builder->update(array($model->getApprovedAtColumn() => $this->now()));
		});
	}

	/**
	 * Add the reject extension to the builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @return void
	 */
	public function addReject( Builder $builder, Model $model )
	{
		$builder->macro('reject', function(Builder $builder) use( $model )
		{
			$builder->withUnapproved();

			return $builder->update(array($model->getApprovedAtColumn() => $this->rejected));
		});
	}

	/**
	 * Determine if the given where clause is an approval constraint.
	 *
	 * @todo make this right
	 *
	 * @param  array   $where
	 * @param  string  $column
	 * @return bool
	 */
	protected function isApprovalConstraint(array $where, $column)
	{
		return $where['type'] == 'Basic' &&
			$where['column'] == $column &&
			($where['operator'] == '<=' || $where['operator'] == '!=');
	}

	/**
	 * Make a new NOW() db expression
	 * @return Expression
	 */
	public function now()
	{
		return new Expression('NOW()');
	}

}
