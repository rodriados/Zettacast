<?php
/**
 * Zettacast\Collection\Permission\Readable trait file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Collection\Permission;

/**
 * Readable contract. Implements all methods that allow data to be read from
 * the collection. Without this contract, no data can be accessed from outside.
 * @package Zettacast\Collection\Permission
 */
trait Readable {
	
	/**
	 * Allows access to data using object notation.
	 * @param mixed $name Data to be accessed in collection.
	 * @return mixed Accessed data.
	 */
	final public function __get($name) {
		
		return $this->get($name, null);
		
	}
	
	/**
	 * Get an element stored in collection.
	 * @param mixed $key Key of requested element.
	 * @param mixed $default Default value fallback.
	 * @return mixed Requested element or default fallback.
	 */
	public function get($key, $default = null) {
		
		return $this->has($key) ? $this->data[$key] : $default;
		
	}
	
	/**
	 * Access data in collection using array notation.
	 * @param mixed $offset Offset to be accessed.
	 * @return mixed Offset value.
	 */
	final public function offsetGet($offset) {
		
		return $this->get($offset);
		
	}
	
}
