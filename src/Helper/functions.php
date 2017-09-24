<?php
/**
 * Zettacast helper functions file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */

use Zettacast\Zettacast;
use Zettacast\Facade\Config;
use Zettacast\Contract\ListableInterface;

if(!function_exists('config')) {
	/**
	 * Retrieves a configuration value from repository.
	 * @param string $key Key to be retrieved from repository.
	 * @param mixed $default Value to be returned if key cannot be retrieved.
	 * @return mixed The retrieved value, or default if not found.
	 */
	function config(string $key, $default = null)
	{
		return Config::get($key, $default);
	}
}

if(!function_exists('isListable')) {
	/**
	 * Checks whether the given variable is listable.
	 * @param mixed $data Variable to be checked.
	 * @return bool Is the variable listable?
	 */
	function isListable($data): bool
	{
		return is_array($data)
			|| $data instanceof ListableInterface
			|| $data instanceof Traversable;
	}
}

if(!function_exists('toArray')) {
	/**
	 * Transforms given data into an array.
	 * @param mixed $data Data to be transformed into array.
	 * @return array Given data as array.
	 */
	function toArray($data): array
	{
		if($data instanceof ListableInterface)
			return $data->raw();
		
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
	 * @return Zettacast|mixed Requested abstraction instance.
	 */
	function zetta(string $abstract = null, ...$args)
	{
		return is_null($abstract)
			? Zettacast::i()
			: Zettacast::i()->make($abstract, $args);
	}
}
