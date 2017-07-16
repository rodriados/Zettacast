<?php
/**
 * Zettacast helper functions file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */

if(!function_exists('dump')) {
	/**
	 * Dumps the passed variables and ends execution.
	 * @param array ...$vars Variables to be dumped.
	 */
	function dump(...$vars)
	{
		var_dump(...$vars);
		exit;
	}
}

if(!function_exists('toarray')) {
	/**
	 * Transforms given data into an array.
	 * @param mixed $data Data to be transformed into array.
	 * @return array Given data as array.
	 */
	function toarray($data)
	{
		if($data instanceof \Zettacast\Contract\Collection\Listable)
			return $data->all();
		
		if($data instanceof Traversable)
			return iterator_to_array($data);
		
		return is_array($data)
			? $data
			: [$data];
	}
}

if(!function_exists('with')) {
	/**
	 * Returns given object. This is useful for method chaining.
	 * @param mixed $object Object to be returned.
	 * @return mixed Given object.
	 */
	function with($object)
	{
		return $object instanceof Closure
			? $object()
			: $object;
	}
}

if(!function_exists('zetta')) {
	/**
	 * Gets the current framework instance.
	 * @param string $abstract Abstraction to be made.
	 * @return Zettacast\Zettacast|mixed Requested abstraction instance.
	 */
	function zetta(string $abstract = null)
	{
		if(is_null($abstract))
			return Zettacast\Zettacast::instance();
		
		return Zettacast\Zettacast::instance()->make($abstract);
	}
}
