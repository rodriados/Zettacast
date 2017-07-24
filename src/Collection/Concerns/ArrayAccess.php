<?php
/**
 * Zettacast\Collection\Concerns\ArrayAccess trait file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Collection\Concerns;

/**
 * This trait implements methods needed for a class to allow array-like access
 * to its contents.
 * @package Zettacast\Collection
 * @version 1.0
 */
trait ArrayAccess
{
	/**
	 * Checks whether an offset exists in collection.
	 * @param mixed $offset Offset to be checked.
	 * @return bool Does the offset exist?
	 */
	final public function offsetExists($offset) : bool
	{
		return $this->has($offset);
	}
	
	/**
	 * Access data in collection using array notation.
	 * @param mixed $offset Offset to be accessed.
	 * @return mixed Offset value.
	 */
	final public function offsetGet($offset)
	{
		return $this->get($offset);
	}
	
	/**
	 * Sets data in collection using array notation.
	 * @param mixed $offset Offset to be set.
	 * @param mixed $value Data to be saved.
	 */
	final public function offsetSet($offset, $value)
	{
		return $this->set($offset, $value);
	}
	
	/**
	 * Erases data in collection using array notation.
	 * @param mixed $offset Offset to be erased.
	 */
	final public function offsetUnset($offset)
	{
		return $this->remove($offset);
	}
	
	/**
	 * Gets an element stored in collection.
	 * @param mixed $key Key of requested element.
	 * @return mixed Requested element or default fallback.
	 */
	abstract public function get($key);
	
	/**
	 * Checks whether element key exists.
	 * @param mixed $key Key to be check if exists.
	 * @return bool Does key exist?
	 */
	abstract public function has($key) : bool;
	
	/**
	 * Removes an element from collection.
	 * @param mixed $key Key to be removed.
	 */
	abstract public function remove($key);
	
	/**
	 * Sets a value to the given key.
	 * @param mixed $key Key to created or updated.
	 * @param mixed $value Value to be stored in key.
	 */
	abstract public function set($key, $value);
	
}
