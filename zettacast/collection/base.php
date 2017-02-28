<?php
/**
 * Zettacast\Collection\Base abstract class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

use Traversable;
use Zettacast\Collection\Contract\Collection;

/**
 * Base collection abstraction. This class has methods appliable for all kinds
 * of collections. Some methods are yet to implemented by inheritance.
 * @package Zettacast\Collection
 * @see \Zettacast\Collection
 * @version 1.0
 */
abstract class Base implements Collection {
	
	/**
	 * Data to be stored.
	 * @var array Data stored in collection.
	 */
	protected $data = [];
	
	/**
	 * Base constructor. This constructor simply sets the data received as the
	 * data stored in collection.
	 * @param array|Collection|\Traversable $data Data to be stored.
	 */
	public function __construct($data = null) {
		
		$this->data = !is_null($data) ? self::toarray($data) : [];
		
	}
	
	/**
	 * Allows access to data using object notation.
	 * @param mixed $name Data to be accessed in collection.
	 * @return mixed Accessed data.
	 */
	final public function __get($name) {
		
		return $this->get($name);
		
	}
		
	/**
	 * Checks whether data exists using object notation.
	 * @param mixed $name Data name to be checked existence.
	 * @return bool Is offset set?
	 */
	final public function __isset($name) {
		
		return $this->has($name);
		
	}
	
	/**
	 * Sets or updates data stored using object notation.
	 * @param mixed $name Data name to be stored.
	 * @param mixed $value Value to be stored.
	 */
	final public function __set($name, $value) {
		
		$this->set($name, $value);
		
	}
		
	/**
	 * Erases data stored using object notation.
	 * @param mixed $name Data name to be erased.
	 */
	final public function __unset($name) {
		
		$this->del($name);
		
	}
		
	/**
	 * Returns all data stored in collection.
	 * @return array All data stored in collection.
	 */
	public function all() {
		
		return $this->data;
		
	}
	
	/**
	 * Creates a copy of collection.
	 * @return static Copied collection.
	 */
	public function copy() {
		
		return new static($this->data);
		
	}
	
	/**
	 * Counts the number of elements currently in collection.
	 * @return int Number of elements stored in the collection.
	 */
	public function count() {
		
		return count($this->data);
		
	}
	
	/**
	 * Return the element the internal pointer currently points to.
	 * @return mixed Current element in the collection.
	 */
	public function current() {
		
		return current($this->data);
		
	}
	
	/**
	 * Checks whether collection is currently empty.
	 * @return bool Is collection empty?
	 */
	public function empty() {
		
		return empty($this->data);
		
	}
	
	/**
	 * Sets the internal pointer of the collection to its last position.
	 * @return mixed Element in the last position.
	 */
	public function end() {
		
		return end($this->data);
		
	}
	
	/**
	 * Checks whether element key exists.
	 * @param mixed $key Key to be check if exists.
	 * @return bool Does key exist?
	 */
	public function has($key) {
		
		return isset($this->data[$key]);
		
	}
	
	/**
	 * Checks whether an element exists in collection.
	 * @param mixed $needle Element being searched for.
	 * @param bool $strict Should types be strictly the same?
	 * @return bool Was the element found?
	 */
	public function in($needle, bool $strict = false) {
		
		return in_array($needle, $this->data, $strict);
		
	}
	
	/**
	 * Creates a generator that iterates over the collection.
	 * @yield mixed Collection's stored values.
	 */
	public function iterate() {
		
		yield from $this->data;
		
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
	 * Passes the collection to the given function and returns the result.
	 * @param callable $fn Function to which collection is passed to.
	 * @return mixed Callback's return result.
	 */
	public function pipe(callable $fn) {
		
		return $fn($this);
		
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
	 * Passes the collection to the given function and returns it.
	 * @param callable $fn Function to which collection is passed to.
	 * @return static Collection's copy sent to function.
	 */
	public function tap(callable $fn) {
		
		$fn($copy = $this->copy());
		return $copy;
		
	}
	
	/**
	 * Checks whether the pointer is a valid position.
	 * @return bool Is the pointer in a valid position?
	 */
	public function valid() {
		
		return key($this->data) !== null;
		
	}
	
	/**
	 * Removes an element from collection.
	 * @param mixed $key Key to be removed.
	 */
	public abstract function del($key);
	
	/**
	 * Get an element stored in collection.
	 * @param mixed $key Key of requested element.
	 * @param mixed $default Default value fallback.
	 * @return mixed Requested element or default fallback.
	 */
	public abstract function get($key, $default = null);
	
	/**
	 * Sets a value to the given key.
	 * @param mixed $key Key to created or updated.
	 * @param mixed $value Value to be stored in key.
	 */
	public abstract function set($key, $value);
	
	/**
	 * Checks whether an offset exists in collection.
	 * @param mixed $offset Offset to be checked.
	 * @return bool Does the offset exist?
	 */
	final public function offsetExists($offset) {
		
		return $this->has($offset);
		
	}
	
	/**
	 * Access data in collection using array notation.
	 * @param mixed $offset Offset to be accessed.
	 * @return mixed Offset value.
	 */
	final public function offsetGet($offset) {
		
		return $this->get($offset);
		
	}
	
	/**
	 * Sets data in collection using array notation.
	 * @param mixed $offset Offset to be set.
	 * @param mixed $value Data to be saved.
	 */
	final public function offsetSet($offset, $value) {
		
		$this->set($offset, $value);
		
	}
	
	/**
	 * Erases data in collection using array notation.
	 * @param mixed $offset Offset to be erased.
	 */
	final public function offsetUnset($offset) {
		
		$this->del($offset);
		
	}
		
	/**
	 * Checks whether data can be converted into a collection.
	 * @param mixed $data Data to be checked if collectible.
	 * @return bool Is it possible data to be a collection?
	 */
	final static protected function listable($data) {
		
		return is_array($data)
			or $data instanceof Collection
			or $data instanceof Traversable;
		
	}
	
	/**
	 * Transforms given data into an array.
	 * @param mixed $data Data to be transformed into array.
	 * @return array Given data as array.
	 */
	final static protected function toarray($data) {
		
		if(is_array($data)) return $data;
		elseif($data instanceof Collection) return $data->all();
		elseif($data instanceof Traversable) return iterator_to_array($data);
		
		return [$data];
		
	}
	
	/**
	 * Creates a new collection mantaining the reference to the original
	 * variable that is the data stored in it.
	 * @param mixed $data Data to be stored in collection.
	 * @return static New collection with referenced data.
	 */
	protected static function ref(&$data) {
		
		if(!is_array($data) and !$data instanceof Collection)
			return $data;
		
		$refobj = new static;
		$refobj->data = &$data;
		
		return $refobj;
		
	}
	
}
