<?php namespace Permissions;

use Mockery as m;

use Vi\Core\Auth\Permissions\PermissionsTrait;

class PermissionsTraitTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Initialise the tests
	 */
	public function setUp()
	{
		parent::setUp();

		$this->permissions = new PermissionsTraitInstance;
	}

	public function tearDown()
	{
		m::close();
	}

	/**
	 * Test the model accessor
	 *
	 * @return void
	 */
	public function testAccessor()
	{
		$this->permissions->attributes['permissions'] = '[1, 2, 3]';
		$this->assertEquals([1, 2, 3], $this->permissions->getPermissionsAttribute());
	}

	/**
	 * Test the model mutator
	 *
	 * @return void
	 */
	public function testMutator()
	{
		$this->permissions->setPermissionsAttribute(['foo', 'bar']);
		$this->assertEquals('["bar","foo"]', $this->permissions->attributes['permissions']);
	}

	/**
	 * Test the model mutator fails
	 *
	 * @return void
	 */
	public function testMutatorOnlyAddsAllowedKeys()
	{
		$this->permissions->setPermissionsAttribute(['foo', 'woop', 'bar']);
		$this->assertEquals('["bar","foo"]', $this->permissions->attributes['permissions']);
	}

	/**
	 * Test the permissions checker
	 *
	 * @return void
	 */
	public function testCan()
	{
		$this->permissions->attributes['permissions'] = '["foo", "bar"]';

		$this->assertTrue( $this->permissions->can('foo') );
		$this->assertTrue( $this->permissions->can(['foo', 'bar']) );
		$this->assertTrue( $this->permissions->can('foo', 'bar') );
	}

	/**
	 * Test the permissions checker
	 *
	 * @return void
	 */
	public function testCanNot()
	{
		$this->permissions->attributes['permissions'] = '["foo", "bar"]';

		$this->assertFalse( $this->permissions->can('foo', 'baz') );
	}

	/**
	 * Test the permissions checker
	 *
	 * @return void
	 */
	public function testCanNotButIsNinjaSoCan()
	{
		$this->permissions->attributes['permissions'] = '["foo", "bar", "ninja"]';

		$this->assertTrue( $this->permissions->can('baz') );
	}

	/**
	 * Test the permissions checker
	 *
	 * @return void
	 */
	public function testCanDoAny()
	{
		$this->permissions->attributes['permissions'] = '["foo", "bar"]';

		$this->assertTrue( $this->permissions->canDoAny('foo', 'baz') );
	}

	/**
	 * Test the permissions checker
	 *
	 * @return void
	 */
	public function testCanNotDoAny()
	{
		$this->permissions->attributes['permissions'] = '["foo", "bar"]';

		$this->assertFalse( $this->permissions->canDoAny('woop', 'boom') );
	}

	/**
	 * Test the permissions checker
	 *
	 * @return void
	 */
	public function testCanNotDoAnyButIsNinjaSoCan()
	{
		$this->permissions->attributes['permissions'] = '["foo", "bar"]';

		$this->assertTrue( $this->permissions->canDoAny('foo', 'baz') );
	}


	/**
	 * Test the permissions granter
	 *
	 * @return void
	 */
	public function testGrant()
	{
		$this->permissions->attributes['permissions'] = '["foo"]';

		$this->permissions->grant('bar');

		$this->assertEquals( ['bar', 'foo'], $this->permissions->currentPermissions() );
	}

	/**
	 * Test the permissions granter
	 *
	 * @return void
	 */
	public function testGrantFails()
	{
		$this->permissions->attributes['permissions'] = '["foo"]';

		$this->permissions->grant(['bar', 'baz']);

		$this->assertEquals( ['bar', 'foo'], $this->permissions->currentPermissions() );
	}

	/**
	 * Test the permissions granter
	 *
	 * @return void
	 */
	public function testDeny()
	{
		$this->permissions->attributes['permissions'] = '["foo", "bar"]';

		$this->permissions->deny('foo');
		$this->assertEquals( ['bar'], $this->permissions->currentPermissions() );
	}

	/**
	 * Test the scope
	 *
	 * @return void
	 */
	public function testNinjaScope()
	{
		$builder = m::mock('Illuminate\Database\Eloquent\Builder');

		$builder->shouldReceive('where')
			->with('permissions', 'LIKE', '%"ninja"%')
			->once();

		$this->permissions->scopeWhereNinja($builder);
	}

	/**
	 * Test the scope
	 *
	 * @return void
	 */
	public function testNotNinjaScope()
	{
		$builder = m::mock('Illuminate\Database\Eloquent\Builder');

		$builder->shouldReceive('where')
			->with('permissions', 'NOT LIKE', '%"ninja"%')
			->once();

		$this->permissions->scopeWhereNotNinja($builder);
	}

	/**
	 * Test the scope
	 *
	 * @return void
	 */
	public function testCanScope()
	{
		$this->markTestIncomplete(
			'The scope of this query scope has changed slightly.'
		);

		$builder = m::mock('Illuminate\Database\Eloquent\Builder');

		$builder->shouldReceive('where')->with('permissions', 'LIKE', '%foo%')->once();
		$builder->shouldReceive('where')->with('permissions', 'LIKE', '%bar%')->once();

		$result = $this->permissions->scopeWhereCan($builder, ['foo', 'bar']);

		$this->assertSame($builder, $result);
	}

	/**
	 * Test the scope
	 *
	 * @return void
	 */
	public function testCanDoAnyScope()
	{
		$this->markTestIncomplete(
			'The scope of this query scope has changed slightly.'
		);

		$builder = m::mock('Illuminate\Database\Eloquent\Builder');

		$builder->shouldReceive('whereNested')->once();

		$result = $this->permissions->scopeWhereCanDoAny($builder, ['foo', 'bar']);

		$this->assertSame($builder, $result);
	}
}

/**
 * A Test class for the permissions trait
 */
class PermissionsTraitInstance {

	use PermissionsTrait;

	public function availablePermissions()
	{
		return ['foo', 'bar'];
	}

	public $attributes = [
		'permissions' => '[]',
	];

}
