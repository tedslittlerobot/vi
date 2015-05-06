<?php namespace Vi\Display\Seo\Meta;

trait MetaDriver {

	/**
	 * Generate a meta tag
	 *
	 * @param  string $key
	 * @param  string $value
	 * @return string
	 */
	public function meta( $key, $value )
	{
		return sprintf('<meta name="%s" content="$s" />', e($key), e($value));
	}
}
