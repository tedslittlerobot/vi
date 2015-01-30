<?php namespace Helpers;

use Mockery as m;

class OptionFilterTest extends \PHPUnit_Framework_TestCase {

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

	public function testItemFiltersByOptions()
	{
		$this->assertFalse(
			item_filtered_by_options( 'foo', [] )
		);
		$this->assertTrue(
			item_filtered_by_options( 'foo', ['only' => 'bar'] )
		);
		$this->assertFalse(
			item_filtered_by_options( 'foo', ['except' => 'bar'] )
		);
		$this->assertFalse(
			item_filtered_by_options( 'foo', ['only' => ['foo']] )
		);
		$this->assertTrue(
			item_filtered_by_options( 'foo', ['except' => ['foo']] )
		);
		$this->assertTrue(
			item_filtered_by_options( 'foo', ['only' => ['foo'], 'except' => ['foo']] )
		);
	}

	public function testItemAllowsByOptions()
	{
		$this->assertTrue(
			item_allowed_by_options( 'foo', [] )
		);
		$this->assertFalse(
			item_allowed_by_options( 'foo', ['only' => 'bar'] )
		);
		$this->assertTrue(
			item_allowed_by_options( 'foo', ['except' => 'bar'] )
		);
		$this->assertTrue(
			item_allowed_by_options( 'foo', ['only' => ['foo']] )
		);
		$this->assertFalse(
			item_allowed_by_options( 'foo', ['except' => ['foo']] )
		);
		$this->assertFalse(
			item_allowed_by_options( 'foo', ['only' => ['foo'], 'except' => ['foo']] )
		);
	}

	public function testArrayFilterWithNoFilters()
	{
		$input = ['foo', 'bar', 'baz', 'foobar', 'foobarbaz'];

		$only = [];

		$except = [];

		$expected = $input;

		$this->assertEquals( $expected, array_filter_by_options($input, $only, $except) );
	}

	public function testArrayFilterWithOnly()
	{
		$input = ['foo', 'bar', 'baz', 'foobar', 'foobarbaz'];

		$only = ['foo', 'bar', 'woop'];

		$except = [];

		$expected = ['foo', 'bar'];

		$this->assertEquals( $expected, array_filter_by_options($input, $only, $except) );
	}

	public function testArrayFilterWithExcept()
	{
		$input = ['foo', 'bar', 'baz', 'foobar', 'foobarbaz'];

		$only = [];

		$except = ['foo', 'bar', 'woop'];

		$expected = ['baz', 'foobar', 'foobarbaz'];

		$this->assertEquals( $expected, array_filter_by_options($input, $only, $except) );
	}

	public function testArrayFilterWithAllOptions()
	{
		$input = ['foo', 'bar', 'baz', 'foobar', 'foobarbaz'];

		$only = ['foo', 'foobar', 'foobarbaz'];

		$except = ['foo', 'bar', 'woop'];

		$expected = ['foobar', 'foobarbaz'];

		$this->assertEquals( $expected, array_filter_by_options($input, $only, $except) );
	}

	public function testArrayFilterExtractsOptionsFromInput()
	{
		$input = ['foo', 'bar', 'baz', 'foobar', 'foobarbaz'];

		$input['only'] = ['foo', 'foobar', 'foobarbaz'];

		$input['except'] = ['foo', 'bar', 'woop'];

		$this->assertSame(
			array_filter_by_options($input, $input['only'], $input['except']),
			array_filter_by_options($input)
		);
	}

	public function testArrayFilterFlattensAssociativeKeys()
	{
		$input = ['foo', 'bar'];

		$input['baz'] = 'baz';

		$this->assertEquals( ['foo', 'bar', 'baz'], array_filter_by_options($input) );
	}

}
