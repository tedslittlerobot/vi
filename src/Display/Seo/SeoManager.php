<?php namespace Vi\Display\Seo;

use Illuminate\Support\Manager;
use Illuminate\Support\Traits\Macroable;

class SeoManager extends Manager {

	/**
	 * A friendly forward to the driver method
	 *
	 * @todo come up with a better name for this
	 *
	 * @param  string $scope
	 * @return mixed
	 */
	public function scope( $scope )
	{
		return $this->driver( $scope );
	}

	/**
	 * Construct a Meta SEO driver
	 * @return \Vi\Display\Seo\MetaDriver
	 */
	public function createMetaDriver()
	{
		return new MetaDriver;
	}

	/**
	 * Construct a OpenGraph SEO driver
	 * @return \Vi\Display\Seo\OpenGraphDriver
	 */
	public function createOpenGraphDriver()
	{
		return new OpenGraphDriver;
	}

	/**
	 * Construct a Robots SEO driver
	 * @return \Vi\Display\Seo\RobotsDriver
	 */
	public function createRobotsDriver()
	{
		return new RobotsDriver;
	}

	/**
	 * Construct a Schema SEO driver
	 * @return \Vi\Display\Seo\SchemaDriver
	 */
	public function createSchemaDriver()
	{
		return new SchemaDriver;
	}

}
