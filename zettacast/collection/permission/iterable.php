<?php
/**
 * Zettacast\Collection\Permission\Iterable trait file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Collection\Permission;

/**
 * Iterable contract. Implements all methods that allow a collection to be
 * iterable by the usage of foreach.
 * @package Zettacast\Collection\Permission
 */
trait Iterable {
	
	/**
	 * Return the element the internal pointer currently points to.
	 * @return mixed Current element in the collection.
	 */
	public function current() {
		
		return current($this->data);
		
	}
	
	/**
	 * Sets the internal pointer of the collection to its last position.
	 * @return mixed Element in the last position.
	 */
	public function end() {
		
		return end($this->data);
		
	}
	
	/**
	 * Fetches the key the internal pointer currently points to.
	 * @return mixed Current element's key in the collection.
	 */
	public function key() {
		
		return key($this->data);
		
	}
	
	/**
	 * Advances the internal pointer one position.
	 * @return mixed Element in the next position.
	 */
	public function next() {
		
		return next($this->data);
		
	}
	
	/**
	 * Rewinds the internal pointer one position.
	 * @return mixed Element in the previous position.
	 */
	public function prev() {
		
		return prev($this->data);
		
	}
	
	/**
	 * Set the internal pointer of the collection to its first element.
	 * @return mixed First element in collection.
	 */
	public function rewind() {
		
		return reset($this->data);
		
	}
	
	/**
	 * Checks whether the pointer is a valid position.
	 * @return bool Is the pointer in a valid position?
	 */
	public function valid() {
		
		return key($this->data) !== null;
		
	}
	
}
