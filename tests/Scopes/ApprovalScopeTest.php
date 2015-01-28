<?php

use Mockery as m;

use Illuminate\Database\Query\Expression;

use Vi\Scopes\ApprovalScope;

class ApprovalScopeTest extends \PHPUnit_Framework_TestCase {

	public function testApplyingScopeToABuilder()
	{
		$scope = m::mock( ApprovalScope::class . '[extend,now]' );

		$scope->shouldReceive('now')->andReturn('now');

		$builder = m::mock('Illuminate\Database\Eloquent\Builder');
		$model = m::mock('Illuminate\Database\Eloquent\Model');

		$model->shouldReceive('getQualifiedApprovedAtColumn')->times(2)->andReturn('table.approved_at');
		$builder->shouldReceive('where')->once()->with('table.approved_at', '<=', 'now')->andReturn($builder);
		$builder->shouldReceive('where')->once()->with('table.approved_at', '!=', '0000-00-00 00:00:00')->andReturn($builder);
		$scope->shouldReceive('extend')->once();

		$scope->apply($builder, $model);
	}

	// public function testScopeCanRemoveApprovedAtConstraints()
	// {
	// 	$scope = new ApprovalScope;
	// 	$builder = m::mock('Illuminate\Database\Eloquent\Builder');
	// 	$builder->shouldReceive('getModel')->andReturn($model = m::mock('StdClass'));
	// 	$model->shouldReceive('getQualifiedApprovedAtColumn')->andReturn('table.approved_at');
	// 	$builder->shouldReceive('getQuery')->andReturn($query = m::mock('StdClass'));
	// 	$query->wheres = [['type' => 'Null', 'column' => 'foo'], ['type' => 'Null', 'column' => 'table.approved_at']];
	// 	$scope->remove($builder);

	// 	$this->assertEquals($query->wheres, [['type' => 'Null', 'column' => 'foo']]);
	// }

	// public function testWhereApprovedExtension()
	// {
	// 	$builder = m::mock('Illuminate\Database\Eloquent\Builder');
	// 	$builder->shouldDeferMissing();
	// 	$scope = m::mock( ApprovalScope::class . '[now]' );
	// 	$scope->extend($builder);

	// 	$scope->shouldReceive('now')->andReturn('now');

	// 	$callback = $builder->getMacro('whereApproved');
	// 	$givenBuilder = m::mock('Illuminate\Database\Eloquent\Builder');
	// 	$givenBuilder->shouldReceive('getModel')->andReturn($model = m::mock('StdClass'));
	// 	$model->shouldReceive('getQualifiedApprovedAtColumn')->times(2)->andReturn('table.approved_at');
	// 	$givenBuilder->shouldReceive('where')->once()->with('table.approved_at', '<=', 'now')->andReturn($builder);
	// 	$givenBuilder->shouldReceive('where')->once()->with('table.approved_at', '!=', '0000-00-00 00:00:00')->andReturn($builder);

	// 	$this->assertSame( $builder, $callback($givenBuilder) );
	// }


	public function testApproveExtension()
	{
		$builder = m::mock('Illuminate\Database\Eloquent\Builder');
		$builder->shouldDeferMissing();

		$model = m::mock('Illuminate\Database\Eloquent\Model');

		$scope = m::mock( ApprovalScope::class . '[now]' );
		$scope->extend($builder, $model);

		$scope->shouldReceive('now')->andReturn('now');

		$callback = $builder->getMacro('approve');
		$givenBuilder = m::mock('Illuminate\Database\Eloquent\Builder');
		$givenBuilder->shouldReceive('whereUnapproved')->once();
		$model->shouldReceive('getApprovedAtColumn')->once()->andReturn('approved_at');

		$givenBuilder->shouldReceive('update')->once()->with(['approved_at' => 'now'])->andReturn('update-response');

		$this->assertEquals( 'update-response', $callback($givenBuilder) );
	}

	public function testRejectExtension()
	{
		$builder = m::mock('Illuminate\Database\Eloquent\Builder');
		$builder->shouldDeferMissing();

		$model = m::mock('Illuminate\Database\Eloquent\Model');

		$scope = new ApprovalScope;
		$scope->extend($builder, $model);

		$callback = $builder->getMacro('reject');
		$givenBuilder = m::mock('Illuminate\Database\Eloquent\Builder');
		$givenBuilder->shouldReceive('withUnapproved')->once();
		$model->shouldReceive('getApprovedAtColumn')->once()->andReturn('approved_at');

		$givenBuilder->shouldReceive('update')->once()->with(['approved_at' => '0000-00-00 00:00:00'])->andReturn('update-response');

		$this->assertEquals( 'update-response', $callback($givenBuilder) );
	}
}
