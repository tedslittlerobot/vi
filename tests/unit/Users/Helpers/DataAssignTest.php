<?php namespace Helpers;

use Mockery as m;

class DataAssignTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Initialise the tests
	 */
	public function setUp()
	{
		parent::setUp();
	}

	public function tearDown()
	{
		m::close();
	}

	public function testBasicAssignation()
	{
		$model = new DataAssignationStub;

		$map = 'foo';

		$this->assertEquals( $model, data_assign( $model, $this->getData(), $map ) );
		$this->assertEquals( ['foo' => 'Foo'], $model->data );
	}

	public function testMultipleAssignation()
	{
		$model = new DataAssignationStub;

		$map = ['foo', 'baz'];

		$this->assertEquals( $model, data_assign( $model, $this->getData(), $map ) );
		$this->assertEquals( ['foo' => 'Foo', 'baz' => 'Baz'], $model->data );
	}

	public function testMissingAssignationSkips()
	{
		$model = new DataAssignationStub;

		$map = ['foo', 'woop'];

		$this->assertEquals( $model, data_assign( $model, $this->getData(), $map ) );
		$this->assertEquals( ['foo' => 'Foo'], $model->data );
	}

	public function testMappedAssignation()
	{
		$model = new DataAssignationStub;

		$map = [
			'newfoo' => 'foo',
			'nested' => 'foobar.foo',
		];

		$this->assertEquals( $model, data_assign( $model, $this->getData(), $map ) );
		$this->assertEquals( ['newfoo' => 'Foo', 'nested' => 'FooBar/Foo'], $model->data );
	}

	public function testMixedAssignation()
	{
		$model = new DataAssignationStub;

		$map = [
			'nested' => 'foobar.bar',
			'bar', 'baz'
		];

		$this->assertEquals( $model, data_assign( $model, $this->getData(), $map ) );
		$this->assertEquals( ['nested' => 'FooBar/Bar', 'bar' => 'Bar', 'baz' => 'Baz'], $model->data );
	}

	protected function getData()
	{
		return [
			'foo' => 'Foo',
			'bar' => 'Bar',
			'baz' => 'Baz',
			'foobar' => [
				'foo' => 'FooBar/Foo',
				'bar' => 'FooBar/Bar',
			],
		];
	}

}

class DataAssignationStub {

	public $data = [];

	public function __set( $key, $value )
	{
		$this->data[$key] = $value;
	}

}
