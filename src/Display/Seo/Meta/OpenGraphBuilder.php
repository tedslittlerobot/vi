<?php namespace Vi\Display\Seo\Meta;

class OpenGraphBuilder {

	/**
	 * Generate an open graph tag
	 *
	 * @param  string $name
	 * @param  string $value
	 * @return string
	 */
	public function tag( $name, $value )
	{
		return sprintf('<meta property="og:%s" content="%s" />', $name, e($value));
	}

}
