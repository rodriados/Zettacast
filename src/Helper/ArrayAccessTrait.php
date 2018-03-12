<?php
/**
 * Zettacast\Helper\ArrayAccessTrait trait file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Helper;

trait ArrayAccessTrait
{
	/**
	 * Accesses data in object using array notation.
	 * @param mixed $offset Offset to access.
	 * @return mixed Offset value.
	 */
	final public function offsetGet($offset)
	{
		return $this->get($offset);
	}
	
	/**
	 * Checks whether an offset exists in the object.
	 * @param mixed $offset Offset to check existance.
	 * @return bool Does the offset exist?
	 */
	final public function offsetExists($offset): bool
	{
		return $this->has($offset);
	}
	
	/**
	 * Sets data in object using array notation.
	 * @param mixed $offset Offset to create or update.
	 * @param mixed $value Data to save.
	 */
	final public function offsetSet($offset, $value): void
	{
		$this->set($offset, $value);
	}
	
	/**
	 * Deletes data from the object using array notation.
	 * @param mixed $offset Offset to erase.
	 */
	final public function offsetUnset($offset): void
	{
		$this->del($offset);
	}
	
	/**
	 * Gets an element stored in object.
	 * @param mixed $key Key of requested element.
	 * @return mixed Requested element or default fallback.
	 */
	abstract public function get($key);
	
	/**
	 * Checks whether element key exists.
	 * @param mixed $key Key to check existance.
	 * @return bool Does key exist?
	 */
	abstract public function has($key): bool;
	
	/**
	 * Sets a value to given key.
	 * @param mixed $key Key to create or update.
	 * @param mixed $value Value to store in key.
	 */
	abstract public function set($key, $value): void;
	
	/**
	 * Deletes an element from object.
	 * @param mixed $key Key to remove.
	 */
	abstract public function del($key): void;
}
