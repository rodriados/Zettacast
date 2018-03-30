<?php
/**
 * Zettacast\Support\ObjectAccessTrait trait file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Support;

trait ObjectAccessTrait
{
	/**
	 * Object access property magic method.
	 * Accesses data in object using object notation.
	 * @param string $key Property name to access.
	 * @return mixed Property value.
	 */
	final public function __get(string $key)
	{
		return $this->get($key);
	}
	
	/**
	 * Object check property magic method.
	 * Checks whether a property exists in object.
	 * @param string $key Property to check.
	 * @return bool Does the property exist?
	 */
	final public function __isset(string $key): bool
	{
		return $this->has($key);
	}
	
	/**
	 * Object store property magic method.
	 * Sets data in object using object notation.
	 * @param string $key Property to create or update.
	 * @param mixed $value Data to save.
	 */
	final public function __set(string $key, $value): void
	{
		$this->set($key, $value);
	}
	
	/**
	 * Object delete property magic method.
	 * Deletes data from object using object notation.
	 * @param mixed $key Property to erase.
	 */
	final public function __unset($key): void
	{
		$this->del($key);
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
	 * Sets a value to the given key.
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
