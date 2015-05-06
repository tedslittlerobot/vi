<?php namespace Vi\Display\Seo\Meta;

class MetaBuilder {

	use MetaTrait, OpenGraphTrait;

	/**
	 * Generate the charset tag for a page
	 *
	 * @param  string $value
	 * @return
	 */
	public function charset( $value = 'text/html; charset=UTF-8' )
	{
		return sprintf('<meta http-equiv="Content-Type" content="$s" />', e($value));
	}

	/**
	 * Generate a robots tag for a page
	 *
	 * @param  string $value
	 * @return
	 */
	public function robots( $value = 'follow, all' )
	{
		return $this->meta( 'robots', $value );
	}

}
