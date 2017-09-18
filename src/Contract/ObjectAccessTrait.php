<?php
/**
 * Zettacast\Contract\ObjectAccessTrait trait file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Contract;

/**
 * This trait implements methods needed for a class to allow object-like access
 * to its contents.
 * @package Zettacast\Contract
 * @version 1.0
 */
trait ObjectAccessTrait
{
	/**
	 * Access data in object using object notation.
	 * @param mixed $key Offset to be accessed.
	 * @return mixed Offset value.
	 */
	final public function __get($key)
	{
		return $this->get($key);
	}
	
	/**
	 * Checks whether an offset exists in the object.
	 * @param mixed $key Offset to be checked.
	 * @return bool Does the offset exist?
	 */
	final public function __isset($key): bool
	{
		return $this->has($key);
	}
	
	/**
	 * Sets data in object using object notation.
	 * @param mixed $key Offset to be set.
	 * @param mixed $value Data to be saved.
	 */
	final public function __set($key, $value)
	{
		return $this->set($key, $value);
	}
	
	/**
	 * Deletes data from object using object notation.
	 * @param mixed $key Offset to be erased.
	 */
	final public function __unset($key)
	{
		return $this->del($key);
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
