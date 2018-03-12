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
use Zettacast\Zettacast;

if(!function_exists('_')) {
	/**
	 * Shortcut for the i18n translator.
	 * @param string $str String to internationalized.
	 * @return string The internationalized string.
	 */
	function _(string $str): string
	{
		return $str;
	}
}

if(!function_exists('listable')) {
	/**
	 * Check whether the given variable is listable.
	 * @param mixed $data Variable to check.
	 * @return bool Is the variable listable?
	 */
	function listable($data): bool
	{
		return is_array($data)
			or $data instanceof \Zettacast\Helper\ListableInterface
		    or $data instanceof \Traversable;
	}
}

if(!function_exists('toarray')) {
	/**
	 * Transform given data into array.
	 * @param mixed $data Data to transform into array.
	 * @return array Given data as array.
	 */
	function toarray($data): array
	{
		if($data instanceof \Zettacast\Helper\ListableInterface)
			return $data->raw();
		
		if($data instanceof \Traversable)
			return iterator_to_array($data);
		
		return is_array($data)
			? $data
			: [$data];
	}
}

if(!function_exists('with')) {
	/**
	 * Return given object. This is useful for method chaining.
	 * @param mixed $object Object to be returned.
	 * @return mixed Given object.
	 */
	function with($object)
	{
		return $object instanceof \Closure
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
		return !is_null($abstract)
			? Zettacast::i()->make($abstract, $params)
			: Zettacast::i();
	}
}
