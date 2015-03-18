<?php namespace Auth;

use Illuminate\Database\Schema\Builder;

class PermissionsTestDatabase {

	public function up( Builder $schema )
	{
		$schema->create('users', function($table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('permissions');
		});

		$schema->create('groups', function($table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('permissions');
		});
	}

	public function seed()
	{
		//
	}

}
