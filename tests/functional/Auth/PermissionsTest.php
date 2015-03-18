<?php namespace Auth;

use Illuminate\Database\Schema\Builder;

class PermissionsTest extends \TestCase {

	public function initialSetUp()
	{
		$this->nukeDatabase();
		$this->migrate();
	}

	public function testDbHit()
	{
		$this->assertTrue(false);
	}
}
