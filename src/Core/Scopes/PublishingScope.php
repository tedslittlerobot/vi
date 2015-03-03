<?php namespace Vi\Core\Scopes;

use Illuminate\Database\Query\Expression;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ScopeInterface;

class PublishingScope implements ScopeInterface {

	/**
	 * All of the extensions to be added to the builder.
	 *
	 * @var array
	 */
	protected $extensions = ['WithNotPublished', 'WhereUnpublished', 'WherePublishedInFuture', 'Publish', 'Unpublish'];

	/**
	 * Apply the scope to a given Eloquent query builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @return void
	 */
	public function apply(Builder $builder, Model $model)
	{
		$builder->where( $model->getQualifiedPublishedAtColumn(), '<=', new Expression('NOW()') );

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
		$column = $model->getQualifiedPublishedAtColumn();

		$query = $builder->getQuery();

		foreach ((array) $query->wheres as $key => $where)
		{
			if ($this->isPublishingConstraint($where, $column))
			{
				unset($query->wheres[$key]);

				$query->wheres = array_values($query->wheres);
			}
		}
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
	 * Add the withNotPublished extension to the builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @return void
	 */
	public function addWithNotPublished( Builder $builder, Model $model )
	{
		$builder->macro('withNotPublished', function(Builder $builder) use ($model)
		{
			$this->remove($builder);

			return $builder;
		});
	}

	/**
	 * Add the whereUnpublished extension to the builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @return void
	 */
	public function addWhereUnpublished( Builder $builder, Model $model )
	{
		$builder->macro('whereUnpublished', function(Builder $builder) use ($model)
		{
			$this->remove($builder);

			$this->whereNull( $model->getQualifiedPublishedAtColumn() );

			return $builder;
		});
	}

	/**
	 * Add the whereUnpublished extension to the builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @return void
	 */
	public function addWherePublishedInFuture( Builder $builder, Model $model )
	{
		$builder->macro('wherePublishedInFuture', function(Builder $builder) use ($model)
		{
			$this->remove($builder);

			$this->where( $model->getQualifiedPublishedAtColumn(), '>', new Expression('NOW()') );

			return $builder;
		});
	}

	/**
	 * Add the publish extension to the builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @return void
	 */
	public function addPublish( Builder $builder, Model $model )
	{
		$builder->macro('publish', function(Builder $builder) use ($model)
		{
			return $builder->update(array($model->getPublishedAtColumn() => new Expression('NOW()')));
		});
	}

	/**
	 * Add the unpublish extension to the builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @return void
	 */
	public function addUnpublish( Builder $builder, Model $model )
	{
		$builder->macro('unpublish', function(Builder $builder) use ($model)
		{
			return $builder->update(array($model->getPublishedAtColumn() => null));
		});
	}

	/**
	 * Determine if the given where clause is a publishing constraint.
	 *
	 * @todo make this right
	 *
	 * @param  array   $where
	 * @param  string  $column
	 * @return bool
	 */
	protected function isPublishingConstraint(array $where, $column)
	{
		return $where['type'] == 'Basic' &&
			$where['column'] == $column && $where['operator'] == '<=';
	}

}
