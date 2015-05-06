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

	/**
	 * Generate a set of open graph tags for an image
	 *
	 * @param  string $url
	 * @param  string $width
	 * @param  string $height
	 * @param  string $type
	 * @param  string $secure
	 * @return string
	 */
	public function imageTag( $url, $width = null, $height = null, $type = null, $secure = null )
	{
		$output = $this->tag( 'og:image:url', $url );

		$output .= $type   ? $this->tag( 'og:image:type', $type ) : '';
		$output .= $width  ? $this->tag( 'og:image:width', $width ) : '';
		$output .= $height ? $this->tag( 'og:image:height', $height ) : '';
		$output .= $secure ? $this->tag( 'og:image:secure_url', $secure ) : '';

		return $output;
	}

}
