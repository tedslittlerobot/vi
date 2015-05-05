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
	protected static $delimiter = '/';

	public function __construct( $components = [] )
	{
		$this->add( $components );
	}

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
		foreach ( static::parseRaw( $value, $raw ) as $component )
		{
			array_unshift( $this->components, static::sanitize($component) );
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
		foreach ( static::parseRaw( $value, $raw ) as $component )
		{
			$this->components[] = static::sanitize( $component );
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

	/**
	 * Set the root path string
	 *
	 * @param string $base
	 */
	public function setBase( $base = '' )
	{
		$this->base = $base;

		return $this;
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
			$components = array_map($components, [static::class, 'escape']);
		}

		return implode(static::$delimiter, $components);
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
	 * This method looks horrible, but it's to handle various useful sets of
	 * things to pass in
	 *
	 * @param  mixed   $value
	 * @param  boolean $raw
	 * @return array
	 */
	protected static function parseRaw( $value, $raw = false )
	{
		$parsed = [];

		foreach( (array) $value as $newValue )
		{
			if (is_array( $newValue ))
			{
				$parsed = array_merge($parsed, static::parseRaw( $newValue, $raw ));
			}
			else
			{
				foreach( explode(static::$delimiter, $newValue) as $component )
				{
					$parsed[] = static::sanitize( $component );
				}
			}
		}

		return $parsed;
	}

	/**
	 * Escape the given component before output
	 *
	 * @param  string $component
	 * @return string
	 */
	public static function escape( $component )
	{
		return str_replace(static::$delimiter, '\\' . static::$delimiter, $component);
	}

	/**
	 * Trim the delimiter off a newly added component
	 *
	 * @param  string $component
	 * @return string
	 */
	public static function sanitize( $component )
	{
		return trim($component, static::$delimiter);
	}

	/////// STATIC ///////

	/**
	 * Add some paths together
	 *
	 * @param mixed  $components
	 * @param string $base
	 */
	public static function addPaths( $components )
	{
		$components = is_array($components) ? $components : func_get_args();

		$path = (new static)
			->add( $components )
			->setBase( $base )
			->render();
	}

}
