<?php
/**
 * Zettacast functions file.
 * This file is responsible for listing all global functions given by the
 * framework. These are mostly shortcuts for objects.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */

if(!function_exists('with')) {
	/**
	 * Return given object. This is useful for method chaining.
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
	 * Get the current framework instance.
	 * @param string $abstract Abstraction to be made.
	 * @param mixed ...$params Arguments to be passed to constructing object.
	 * @return Zettacast|mixed Requested abstraction instance.
	 */
	function zetta(string $abstract = null, ...$params)
	{
		return $abstract;
	}
}
