<?php

use Mockery as m;

use Carbon\Carbon;

use Vi\Scopes\ApprovalTrait;

class ApprovalTraitTest extends \PHPUnit_Framework_TestCase {

	public function testApproveSetsApprovedColumn()
	{
		$model = m::mock( ApprovalTraitStub::class . '[extend]');
		$model->shouldDeferMissing();

		$model->shouldReceive('newQuery')->andReturn($query = m::mock('StdClass'));
		$query->shouldReceive('withUnapproved')->once()->andReturn($query);
		$query->shouldReceive('where')->once()->with('id', 1)->andReturn($query);
		$query->shouldReceive('update')->once()->with(['approved_at' => 'date-time']);

		$model->approve();

		$this->assertInstanceOf('Carbon\Carbon', $model->approved_at);
	}

	public function testRejectSetsApprovedColumn()
	{
		$model = m::mock( ApprovalTraitStub::class . '[extend]');

		$model->attributes['approved_at'] = 'foo';

		$model->shouldDeferMissing();

		$model->shouldReceive('newQuery')->andReturn($query = m::mock('StdClass'));
		$query->shouldReceive('withUnapproved')->once()->andReturn($query);
		$query->shouldReceive('where')->once()->with('id', 1)->andReturn($query);
		$query->shouldReceive('update')->once()->with(['approved_at' => ApprovalTraitStub::getRejectionFormat()]);

		$model->reject();

		$this->assertEquals( ApprovalTraitStub::getRejectionFormat(), $model->attributes['approved_at'] );
	}

	public function testApprovedAttributeIsPending()
	{
		$model = new ApprovalTraitStub;

		$model->attributes['approved_at'] = null;

		$this->assertEquals( 'pending', $model->getApprovalStatusAttribute() );
	}

	public function testApprovedAttributeIsRejected()
	{
		$model = new ApprovalTraitStub;

		$model->attributes['approved_at'] = ApprovalTraitStub::getRejectionFormat();

		$model->approved_at = Carbon::createFromFormat( '0000-00-00 00:00:00', ApprovalTraitStub::getRejectionFormat() );

		$this->assertEquals( 'rejected', $model->getApprovalStatusAttribute() );
	}

	public function testApprovedAttributeIsApproved()
	{
		$model = new ApprovalTraitStub;

		$model->approved_at = Carbon::yesterday();

		$model->attributes['approved_at'] = $model->approved_at->__toString();

		$this->assertEquals( 'approved', $model->getApprovalStatusAttribute() );
	}

	public function testFullyQualifiedApprovedColumn()
	{
		$model = new ApprovalTraitStub;

		$this->assertEquals( 'table.approved_at', $model->getQualifiedApprovedAtColumn() );
	}

	public function testApprovalSwitchValue()
	{
		$model = m::mock( ApprovalTraitStub::class . '[getApprovalStatusAttribute]' );

		$model->shouldReceive( 'getApprovalStatusAttribute' )->once()->andReturn( 'approved' );

		$response = $model->approvalSwitch( 'foo', [$this, 'returnBar'], function() { return 'baz'; } );

		$this->assertEquals( 'foo', $response );
	}

	public function testApprovalSwitchCallable()
	{
		$model = m::mock( ApprovalTraitStub::class . '[getApprovalStatusAttribute]' );

		$model->shouldReceive( 'getApprovalStatusAttribute' )->once()->andReturn( 'pending' );

		$response = $model->approvalSwitch( 'foo', [$this, 'returnBar'], function() { return 'baz'; } );

		$this->assertEquals( 'bar', $response );
	}

	public function testApprovalSwitchClosure()
	{
		$model = m::mock( ApprovalTraitStub::class . '[getApprovalStatusAttribute]' );

		$model->shouldReceive( 'getApprovalStatusAttribute' )->once()->andReturn( 'rejected' );

		$response = $model->approvalSwitch( 'foo', [$this, 'returnBar'], function() { return 'baz'; } );

		$this->assertEquals( 'baz', $response );
	}

	public function testApprovalSwitchNull()
	{
		$model = m::mock( ApprovalTraitStub::class . '[getApprovalStatusAttribute]' );

		$model->shouldReceive( 'getApprovalStatusAttribute' )->once()->andReturn( 'foo' );

		$response = $model->approvalSwitch( 'foo', [$this, 'returnBar'], function() { return 'baz'; } );

		$this->assertNull( $response );
	}

	public function returnBar()
	{
		return 'bar';
	}
}

class ApprovalTraitStub {

	use ApprovalTrait;

	public $attributes = [];

	public function getKey()
	{
		return 1;
	}
	public function getTable()
	{
		return 'table';
	}
	public function getKeyName()
	{
		return 'id';
	}
	public function save()
	{
		//
	}
	public function delete()
	{
		return $this->performDeleteOnModel();
	}
	public function fireModelEvent()
	{
		//
	}
	public function freshTimestamp()
	{
		return Carbon::now();
	}
	public function fromDateTime()
	{
		return 'date-time';
	}
}
