<?php namespace Scopes;

use Mockery as m;

use Illuminate\Database\Query\Expression;

use Vi\Core\Scopes\PublishingScope;

class PublishingScopeTest extends \PHPUnit_Framework_TestCase {

	public function testApplyingScopeToABuilder()
	{
		$scope = m::mock( PublishingScope::class . '[extend]' );

		$builder = m::mock('Illuminate\Database\Eloquent\Builder');
		$model = m::mock('Illuminate\Database\Eloquent\Model');

		$model->shouldReceive('getQualifiedPublishedAtColumn')->once()->andReturn('table.published_at');
		$builder->shouldReceive('where')->once()/*->with('table.published_at', '<=', 'now')*/->andReturn($builder);
		// $scope->shouldReceive('now')->once()->andReturn('now');
		$scope->shouldReceive('extend')->once();

		$scope->apply($builder, $model);
	}

	// public function testScopeCanRemovePublishedAtConstraints()
	// {
	// 	$scope = new PublishingScope;
	// 	$builder = m::mock('Illuminate\Database\Eloquent\Builder');
	// 	$builder->shouldReceive('getModel')->andReturn($model = m::mock('StdClass'));
	// 	$model->shouldReceive('getQualifiedPublishedAtColumn')->andReturn('table.published_at');
	// 	$builder->shouldReceive('getQuery')->andReturn($query = m::mock('StdClass'));
	// 	$query->wheres = [['type' => 'Null', 'column' => 'foo'], ['type' => 'Null', 'column' => 'table.published_at']];
	// 	$scope->remove($builder);

	// 	$this->assertEquals($query->wheres, [['type' => 'Null', 'column' => 'foo']]);
	// }

	public function testPublishExtension()
	{
		$builder = m::mock('Illuminate\Database\Eloquent\Builder');
		$builder->shouldDeferMissing();

		$model = m::mock('Illuminate\Database\Eloquent\Model');

		$scope = new PublishingScope;
		$scope->extend($builder, $model);

		$callback = $builder->getMacro('publish');
		$givenBuilder = m::mock('Illuminate\Database\Eloquent\Builder');
		$model->shouldReceive('getPublishedAtColumn')->once()->andReturn('published_at');

		$givenBuilder->shouldReceive('update')->once()/*->with(['published_at' => 'now'])*/->andReturn('update-response');

		$this->assertEquals( 'update-response', $callback($givenBuilder) );
	}

	public function testUnpublishExtension()
	{
		$builder = m::mock('Illuminate\Database\Eloquent\Builder');
		$builder->shouldDeferMissing();

		$model = m::mock('Illuminate\Database\Eloquent\Model');

		$scope = new PublishingScope;
		$scope->extend($builder, $model);

		$callback = $builder->getMacro('unpublish');
		$givenBuilder = m::mock('Illuminate\Database\Eloquent\Builder');
		$model->shouldReceive('getPublishedAtColumn')->once()->andReturn('published_at');

		$givenBuilder->shouldReceive('update')->once()->with(['published_at' => null])->andReturn('update-response');

		$this->assertEquals( 'update-response', $callback($givenBuilder) );
	}
}
