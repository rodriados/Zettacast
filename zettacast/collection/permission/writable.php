<?php
/**
 * Zettacast\Collection\Permission\Writable trait file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Collection\Permission;

/**
 * Writable contract. Implements all methods that allow data to be written on
 * collection. Without this contract, no data be written from outside.
 * @package Zettacast\Collection\Permission
 */
trait Writable {
	
	/**
	 * Sets or updates data stored using object notation.
	 * @param mixed $name Data name to be stored.
	 * @param mixed $value Value to be stored.
	 */
	final public function __set($name, $value) {
		
		$this->set($name, $value);
		
	}
	
	/**
	 * Adds an element to the collection if it doesn't exist.
	 * @param mixed $key Key name to be added.
	 * @param mixed $value Value to be stored.
	 */
	public function add($key, $value) {
		
		if(!$this->has($key))
			$this->set($key, $value);
		
	}
	
	/**
	 * Sets a value to the given key.
	 * @param mixed $key Key to created or updated.
	 * @param mixed $value Value to be stored in key.
	 */
	public function set($key, $value) {
		
		$this->data[$key] = $value;
		
	}
	
	/**
	 * Sets data in collection using array notation.
	 * @param mixed $offset Offset to be set.
	 * @param mixed $value Data to be saved.
	 */
	final public function offsetSet($offset, $value) {
		
		$this->set($offset, $value);
		
	}
	
}
