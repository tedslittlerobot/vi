<?php namespace Permissions;

use Mockery as m;

use Vi\Core\Users\UserTrait;

class UserTraitTest extends \TestCase {

	/**
	 * Initialise the tests
	 */
	public function setUp()
	{
		parent::setUp();

		$this->instance = new UserTraitInstance;
	}

	public function tearDown()
	{
		m::close();
	}

	public function testFirstnameAttribute()
	{
		$this->instance->setFirstnameAttribute( 'foo' );

		$this->assertEquals( 'Foo', $this->instance->attributes['firstname'] );
	}

	public function testLastnameAttribute()
	{
		$this->instance->setLastnameAttribute( 'foo' );

		$this->assertEquals( 'Foo', $this->instance->attributes['lastname'] );
	}

	public function testFullnameAttribute()
	{
		$expected = 'foo bar';

		$actual = $this->instance->getFullnameAttribute();

		$this->assertEquals( $expected, $actual );
	}

	public function testNameAttributeDefaultingToFullname()
	{
		$this->instance->nickname = null;

		$actual = $this->instance->getNameAttribute();

		$this->assertEquals( 'foo bar', $actual );
	}

	public function testNameAttributeUsesNickname()
	{
		$this->instance->nickname = 'ron';

		$actual = $this->instance->getNameAttribute();

		$this->assertEquals( 'ron', $actual );
	}

}

/**
 * A Test class for the permissions trait
 */
class UserTraitInstance {

	use UserTrait;

	public function getFirstnameAttribute()
	{
		return 'foo';
	}

	public function getLastnameAttribute()
	{
		return 'bar';
	}

	public function getNicknameAttribute()
	{
		return $this->nickname;
	}

	public $nickname;

	public $attributes = [];

}
