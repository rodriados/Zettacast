<?php
/**
 * Zettacast\Collection\Permission\Erasable trait file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Collection\Permission;

/**
 * Erasable contract. Implements all methods that allow data to be removed from
 * collection. Without this contract, no data can be erased from outside.
 * @package Zettacast\Collection\Permission
 */
trait Erasable {
	
	/**
	 * Erases data stored using object notation.
	 * @param mixed $name Data name to be erased.
	 */
	final public function __unset($name) {
		
		$this->del($name);
		
	}
	
	/**
	 * Removes one or many elements from collection.
	 * @param mixed|array $keys Keys to be forgotten.
	 */
	public function forget($keys) {
		
		foreach(self::convert($keys) as $key)
			$this->del($key);
		
	}
	
	/**
	 * Removes an element from collection.
	 * @param mixed $key Key to be removed.
	 */
	public function del($key) {
		
		if($this->has($key))
			unset($this->data[$key]);
		
	}
	
	/**
	 * Erases data in collection using array notation.
	 * @param mixed $offset Offset to be erased.
	 */
	final public function offsetUnset($offset) {
		
		$this->del($offset);
		
	}
	
}
