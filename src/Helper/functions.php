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

if(!function_exists('listable')) {
	function listable($data) : bool
	{
		return is_array($data)
			or $data instanceof \Zettacast\Contract\Collection\Listable
			or $data instanceof Traversable;
	}
}

if(!function_exists('toarray')) {
	/**
	 * Transforms given data into an array.
	 * @param mixed $data Data to be transformed into array.
	 * @return array Given data as array.
	 */
	function toarray($data) : array
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
	 * @param mixed ...$args Arguments to be passed to constructing object.
	 * @return Zettacast\Zettacast|mixed Requested abstraction instance.
	 */
	function zetta(string $abstract = null, ...$args)
	{
		if(is_null($abstract))
			return \Zettacast\Zettacast::instance();
		
		return \Zettacast\Zettacast::instance()->make($abstract, $args);
	}
}
