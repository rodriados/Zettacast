<?php
/**
 * Zettacast\Contract\Collection\Collection interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Contract\Collection;

/**
 * Collection interface. This interface exposes all methods needed for a class
 * to work as a collection.
 * @package Zettacast\Contract\Collection
 */
interface Collection
	extends Listable
{
	/**
	 * Get an element stored in collection.
	 * @param mixed $key Key of requested element.
	 * @param mixed $default Default value fallback.
	 * @return mixed Requested element or default fallback.
	 */
	public function get($key, $default = null);
	
	/**
	 * Checks whether element key exists.
	 * @param mixed $key Key to be check if exists.
	 * @return bool Does key exist?
	 */
	public function has($key) : bool;
	
	/**
	 * Removes an element from collection.
	 * @param mixed $key Key to be removed.
	 */
	public function remove($key);
	
	/**
	 * Sets a value to the given key.
	 * @param mixed $key Key to created or updated.
	 * @param mixed $value Value to be stored in key.
	 */
	public function set($key, $value);
	
}
