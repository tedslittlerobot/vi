<?php

if ( ! function_exists('data_assign'))
{
	/**
	 * Assign the given data to the model
	 *
	 * @param  object  $model
	 * @param  array                                $data
	 * @param  array|string                         $map
	 * @return object
	 */
	function data_assign( $model, array $data, $map )
	{
		// Get microtime as a safe unique value
		$unique = microtime();

		foreach ( (array) $map as $to => $from )
		{
			// if the element is not associative, map to the same key
			if ( is_int($to) )
				$to = $from;

			// if the value exists in the data, assign it to the model
			if ( ($value = array_get( $data, $from, $unique )) !== $unique )
				$model->{$to} = $value;
		}

		return $model;
	}

}

// ! Only / Exclude Helpers

if ( ! function_exists('array_filter_by_options'))
{
	/**
	 * Filters the array by the given options.
	 *
	 * @param  object  $model
	 * @param  array                                $data
	 * @param  array|string                         $map
	 * @return object
	 */
	function array_filter_by_options( $array, $only = [], $except = [] )
	{
		if ( isset($array['only']) )
			$only = array_merge( array_pull($array, 'only') );

		if ( isset($array['except']) )
			$except = array_merge( array_pull($array, 'except') );

		// only show items in the $only array if it exists
		if ( ! empty( $only ) )
		{
			$array = array_intersect( $array, $only );
		}

		// filter out items in the $except array
		return array_values( array_diff( $array, $except ) );
	}

}

if ( ! function_exists('item_filtered_by_options'))
{
	/**
	 * Determins if the item is excluded by the given options.
	 * Taken from laravel's internals.
	 *
	 * @param  mixed  $item
	 * @param  array  $options
	 * @return boolean
	 */
	function item_filtered_by_options( $item, array $options )
	{
		return (
			( ! empty($options['only']) && ! in_array($item, (array) $options['only'])) ||
			( ! empty($options['except']) && in_array($item, (array) $options['except']))
		);
	}
}

if ( ! function_exists('item_allowed_by_options'))
{
	/**
	 * Determins if the item is allowed by the given options
	 *
	 * @param  mixed  $item
	 * @param  array  $options
	 * @return boolean
	 */
	function item_allowed_by_options( $item, array $options )
	{
		return ! item_filtered_by_options( $item, $options );
	}
}
