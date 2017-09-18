<?php
/**
 * Zettacast\Contract\ArrayAccessTrait trait file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Contract;

/**
 * This trait implements methods needed for a class to allow array-like access
 * to its contents.
 * @package Zettacast\Contract
 * @version 1.0
 */
trait ArrayAccessTrait
{
	/**
	 * Access data in object using array notation.
	 * @param mixed $offset Offset to be accessed.
	 * @return mixed Offset value.
	 */
	final public function offsetGet($offset)
	{
		return $this->get($offset);
	}
	
	/**
	 * Checks whether an offset exists in the object.
	 * @param mixed $offset Offset to be checked.
	 * @return bool Does the offset exist?
	 */
	final public function offsetExists($offset): bool
	{
		return $this->has($offset);
	}
	
	/**
	 * Sets data in object using array notation.
	 * @param mixed $offset Offset to be set.
	 * @param mixed $value Data to be saved.
	 */
	final public function offsetSet($offset, $value)
	{
		return $this->set($offset, $value);
	}
	
	/**
	 * Deletes data from the object using array notation.
	 * @param mixed $offset Offset to be erased.
	 */
	final public function offsetUnset($offset)
	{
		return $this->del($offset);
	}
	
	/**
	 * Gets an element stored in object.
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
	 * Sets a value to the given key.
	 * @param mixed $key Key to created or updated.
	 * @param mixed $value Value to be stored in key.
	 */
	abstract public function set($key, $value);
	
	/**
	 * Deletes an element from object.
	 * @param mixed $key Key to be removed.
	 */
	abstract public function del($key);
	
}
