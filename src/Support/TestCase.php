<?php namespace Vi\Support;

use ReflectionClass;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Schema\Builder;
use Illuminate\Foundation\Testing\TestCase as IlluminateTestCase;

abstract class TestCase extends IlluminateTestCase {

	// ! Helpers

	/**
	 * If the tests have started
	 *
	 * @var bool
	 */
	protected $booted = false;

	/**
	 * Automatically nuke the database at the start and end of the test
	 *
	 * @var boolean
	 */
	protected $shouldNukeDatabase = false;

	/**
	 * Set up the class before the first test is run
	 *
	 * @return void
	 */
	public function initialSetUp() {}

	/**
	 * Clean up the class after the last test is run
	 *
	 * @hack this currently does not execute within the same instance as the
	 * rest of the tests
	 *
	 * @return void
	 */
	public function finalTearDown() {}

	/**
	 * @{inheritdoc}
	 */
	public function setUp()
	{
		parent::setUp();

		if ( ! $this->booted )
		{
			$this->booted = true;

			$this->autoNuke();

			$this->initialSetUp();
		}
	}

	/**
	 * @{inheritdoc}
	 */
	public static function tearDownAfterClass()
	{
		$self = (new static);
		$self->finalTearDown();
		$self->autoNuke();
	}

	// ! Databases

	/**
	 * The default connection to use
	 *
	 * @var string
	 */
	public $connection;

	/**
	 * Parse the connection. If nothing is given, the default connection
	 * is used
	 *
	 * @param  string $connection
	 * @return \Illuminate\Database\ConnectionInterface
	 */
	public function getConnection( $connection = null )
	{
		return $this->app['db']->connection( $connection ?: $this->connection );
	}

	/**
	 * Nuke the database if the flag is set
	 *
	 * @return void
	 */
	protected function autoNuke()
	{
		if ( $this->shouldNukeDatabase )
		{
			$this->nukeDatabase();
		}
	}

	/**
	 * Drop all tables from a database
	 *
	 * @param  string $connection
	 * @return void
	 */
	public function nukeDatabase( $connection = null )
	{
		$connection = $this->getConnection( $connection );

		$db = $connection->getDatabaseName();

		$tables = $connection->select("
			SELECT concat('DROP TABLE IF EXISTS ', table_name, ';')
				FROM information_schema.tables
				WHERE table_schema = '{$db}';
		");

		foreach( $tables as $statement )
		{
			$connection->delete( head((array)$statement) );
		}
	}

	/**
	 * Run the given migration(s)
	 *
	 * @param  array|string $classname
	 * @return void
	 */
	public function migrate()
	{
		$classname = get_class($this) . 'Database';

		$reflection = new ReflectionClass($this);

		$path = dirname($reflection->getFileName()) . DIRECTORY_SEPARATOR . class_basename($classname) . '.php';

		require_once( $path );

		$class = (new $classname);
		$connection = $this->getConnection();

		if (method_exists($class, 'up'))
		{
			$class->up( $connection->getSchemaBuilder() );
		}

		if (method_exists($class, 'seed'))
		{
			$class->seed( $connection );
		}
	}

	/**
	 * Run all the app's normal migrations
	 *
	 * @return void
	 */
	public function migrateApp()
	{
		$this->artisan('db:migrate', ['--env' => 'testing']);
	}

}
