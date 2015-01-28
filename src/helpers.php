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
