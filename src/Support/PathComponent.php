<?php namespace Vi\Support;

class PathComponent {

	/**
	 * The components array
	 *
	 * @var array
	 */
	protected $components = [];

	/**
	 * The base string for the path
	 *
	 * @var string
	 */
	protected $base = '/';

	/**
	 * The component set delimiter
	 *
	 * @var string
	 */
	protected $delimiter = '/';

	/////// MUTATORS ///////

	/**
	 * Add a component to the start of the components array
	 *
	 * @param  string  $value
	 * @param  boolean $raw
	 * @return $this
	 */
	public function prepend( $value, $raw = false )
	{
		foreach ( $this->parseRaw( $value, $raw ) as $component )
		{
			array_unshift($this->components, $component);
		}

		return $this;
	}

	/**
	 * Add a component to the end of the components array
	 *
	 * @param  string  $value
	 * @param  boolean $raw
	 * @return $this
	 */
	public function add( $value, $raw = false )
	{
		foreach ( $this->parseRaw( $value, $raw ) as $component )
		{
			$this->components[] = $component;
		}

		return $this;
	}

	/**
	 * Add a component to the start of the components array
	 *
	 * @param  string  $value
	 * @param  boolean $raw
	 * @return $this
	 */
	public function pop()
	{
		return array_pop( $this->components );
	}

	/////// ACCESSORS ///////

	/**
	 * Get the components array
	 *
	 * @return array
	 */
	public function components()
	{
		return $this->components;
	}

	/**
	 * Get the final component
	 *
	 * @return string
	 */
	public function basename()
	{
		return last( $this->components );
	}

	/**
	 * Get the second to last component
	 *
	 * @return string
	 */
	public function dirname()
	{
		return $this->getComponent( -2 );
	}

	/**
	 * Get a component from the given index. Use negative index to count from
	 * the end
	 *
	 * @param  int   $index
	 * @return string
	 */
	public function getComponent( $index )
	{
		return array_get( $this->components, $this->relativeIndexToKey($index) );
	}

	/**
	 * Convert a positive or negative index into an absolute array key.
	 *
	 * Zero gets the first component. Use a negative index to count from the end
	 * of the components. -1 returns the last component (the same as
	 * `$this->basename()`)
	 *
	 * @param  int $index
	 * @return string
	 */
	public function relativeIndexToKey( $index )
	{
		if ( $index >= 0 )
		{
			return $index;
		}

		return count( $this->components ) + $index;
	}

	/**
	 * Get a slice of the components
	 *
	 * @param  int     $start
	 * @param  int     $end
	 * @param  boolean $escape
	 * @return string
	 */
	public function slice( $start, $end )
	{
		return array_slice(
			$this->components,
			$this->relativeIndexToKey($start),
			$this->relativeIndexToKey($end)
		);
	}

	/////// RENDER ///////

	/**
	 * Compile the given components. Escapes if necessary.
	 *
	 * @param  array  $components
	 * @param  boolean $escape
	 * @return array
	 */
	public function compile( $components, $escape = true )
	{
		if ( $escape )
		{
			$components = array_map($components, [$this, 'escape']);
		}

		return implode($this->delimiter, $components);
	}

	/**
	 * Render the whole path
	 *
	 * @return string
	 */
	public function render()
	{
		return $this->base . $this->compile($this->components);
	}

	/**
	 * Render a slice of the components
	 *
	 * @param  int     $start
	 * @param  int     $end
	 * @param  boolean $escape
	 * @return string
	 */
	public function renderSlice( $start, $end, $escape = true )
	{
		return $this->compile( $this->slice( $start, $end ), $escape );
	}

	/////// HELPERS ///////

	/**
	 * Parse the component into an array of components, optionally spliting the
	 * by the delimiter
	 *
	 * @param  string  $value
	 * @param  boolean $raw
	 * @return array
	 */
	protected function parseRaw( $value, $raw = false )
	{
		return $raw ? (array)$value : explode($this->delimiter, $value);
	}

	/**
	 * Escape the given component
	 *
	 * @param  string $component
	 * @return string
	 */
	public function escape( $component )
	{
		return str_replace($this->delimiter, '\\' . $this->delimiter, $component);
	}

}
