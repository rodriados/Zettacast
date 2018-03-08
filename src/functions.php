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
