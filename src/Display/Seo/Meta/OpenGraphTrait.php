<?php namespace Vi\Display\Seo\Meta;

trait OpenGraphTrait {

	/**
	 * Generate an open graph tag for the given
	 *
	 * @param  string $name
	 * @param  string $value
	 * @return string
	 */
	public function openGraph( $name, $value )
	{
		return sprintf('<meta property="og:%s" content="%s" />', $name, e($value));
	}

}
