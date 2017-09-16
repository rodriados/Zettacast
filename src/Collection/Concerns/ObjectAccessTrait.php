<?php
/**
 * Zettacast\Collection\Concerns\ObjectAccessTrait trait file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Collection\Concerns;

/**
 * This trait implements methods needed for a class to allow object-like access
 * to its contents.
 * @package Zettacast\Collection
 * @version 1.0
 */
trait ObjectAccessTrait
{
	/**
	 * Access data in collection using array notation.
	 * @param mixed $key Offset to be accessed.
	 * @return mixed Offset value.
	 */
	final public function __get($key)
	{
		return $this->get($key);
	}
	
	/**
	 * Checks whether an offset exists in collection.
	 * @param mixed $key Offset to be checked.
	 * @return bool Does the offset exist?
	 */
	final public function __isset($key): bool
	{
		return $this->has($key);
	}
	
	/**
	 * Sets data in collection using array notation.
	 * @param mixed $key Offset to be set.
	 * @param mixed $value Data to be saved.
	 */
	final public function __set($key, $value)
	{
		return $this->set($key, $value);
	}
	
	/**
	 * Erases data in collection using array notation.
	 * @param mixed $key Offset to be erased.
	 */
	final public function __unset($key)
	{
		return $this->del($key);
	}
	
	/**
	 * Get an element stored in collection.
	 * @param mixed $key Key of requested element.
	 * @return mixed Requested element or default fallback.
	 */
	abstract public function get($key);
	
	/**
	 * Checks whether element key exists.
	 * @param mixed $key Key to be check if exists.
	 * @return bool Does key exist?
	 */
	abstract public function has($key): bool;
	
	/**
	 * Removes an element from collection.
	 * @param mixed $key Key to be removed.
	 */
	abstract public function del($key);
	
	/**
	 * Sets a value to the given key.
	 * @param mixed $key Key to created or updated.
	 * @param mixed $value Value to be stored in key.
	 */
	abstract public function set($key, $value);
	
}
