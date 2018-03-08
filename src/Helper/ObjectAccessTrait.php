<?php
/**
 * Zettacast\Helper\ObjectAccessTrait trait file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Helper;

trait ObjectAccessTrait
{
	/**
	 * Access data in object using object notation.
	 * @param string $key Property name to access.
	 * @return mixed Property value.
	 */
	final public function __get(string $key)
	{
		return $this->get($key);
	}
	
	/**
	 * Check whether a property exists in the object.
	 * @param string $key Property to check.
	 * @return bool Does the property exist?
	 */
	final public function __isset(string $key): bool
	{
		return $this->has($key);
	}
	
	/**
	 * Set data in object using object notation.
	 * @param string $key Property to set.
	 * @param mixed $value Data to save.
	 */
	final public function __set(string $key, $value): void
	{
		$this->set($key, $value);
	}
	
	/**
	 * Delete data from object using object notation.
	 * @param mixed $key Property to erase.
	 */
	final public function __unset($key): void
	{
		$this->del($key);
	}
	
	/**
	 * Get an element stored in object.
	 * @param mixed $key Key of requested element.
	 * @return mixed Requested element or default fallback.
	 */
	abstract public function get($key);
	
	/**
	 * Check whether element key exists.
	 * @param mixed $key Key to be check if exists.
	 * @return bool Does key exist?
	 */
	abstract public function has($key): bool;
	
	/**
	 * Set a value to the given key.
	 * @param mixed $key Key to created or updated.
	 * @param mixed $value Value to be stored in key.
	 */
	abstract public function set($key, $value): void;
	
	/**
	 * Delete an element from object.
	 * @param mixed $key Key to be removed.
	 */
	abstract public function del($key): void;
}
