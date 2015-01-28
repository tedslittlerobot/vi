<?php namespace Scopes;

use Mockery as m;

use Carbon\Carbon;

use Vi\Scopes\PublishingScope;
use Vi\Scopes\PublishingTrait;

class PublishingTraitTest extends \PHPUnit_Framework_TestCase {

	public function testBootMethod()
	{
		 PublishingTraitStub::bootPublishingTrait();
	}

	public function testPublishSetsPublishedColumn()
	{
		$model = m::mock( PublishingTraitStub::class . '[extend]');
		$model->shouldDeferMissing();

		$model->shouldReceive('newQuery')->andReturn($query = m::mock('StdClass'));
		$query->shouldReceive('where')->once()->with('id', 1)->andReturn($query);
		$query->shouldReceive('update')->once()->with(['published_at' => 'date-time']);

		$model->publish();

		$this->assertInstanceOf('Carbon\Carbon', $model->published_at);
	}

	public function testUnpublishSetsPublishedColumn()
	{
		$model = m::mock( PublishingTraitStub::class . '[extend]');

		$model->published_at = 'foo';

		$model->shouldDeferMissing();

		$model->shouldReceive('newQuery')->andReturn($query = m::mock('StdClass'));
		$query->shouldReceive('where')->once()->with('id', 1)->andReturn($query);
		$query->shouldReceive('update')->once()->with(['published_at' => null]);

		$model->unpublish();

		$this->assertNull( $model->published_at );
	}

	public function testIsPublishedAttributeIsTrue()
	{
		$model = new PublishingTraitStub;

		$model->published_at = Carbon::yesterday();

		$this->assertTrue( $model->getIsPublishedAttribute() );
	}

	public function testIsPublishedAttributeIsFalseFromNull()
	{
		$model = new PublishingTraitStub;

		$model->published_at = null;

		$this->assertFalse( $model->getIsPublishedAttribute() );
	}

	public function testIsPublishedAttributeIsFalseFromFuture()
	{
		$model = new PublishingTraitStub;

		$model->published_at = Carbon::tomorrow();

		$this->assertFalse( $model->getIsPublishedAttribute() );
	}

	public function testFullyQualifiedPublishedColumn()
	{
		$model = new PublishingTraitStub;

		$this->assertEquals( 'table.published_at', $model->getQualifiedPublishedAtColumn() );
	}

	public function testGetDates()
	{
		$model = new PublishingTraitStub;

		$this->assertEquals(['foo', 'bar', 'published_at'], $model->getDates());
	}
}

class Model {

	public static function addGlobalScope( \Illuminate\Database\Eloquent\ScopeInterface $scope )
	{
		return $scope;
	}

	public function getDates()
	{
		return ['foo', 'bar'];
	}

}

class PublishingTraitStub extends Model {

	use PublishingTrait;

	public $published_at;

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
