<?php
/**
 * Zettacast\Collection\Base abstract class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

use Zettacast\Collection\Permission\Iterable;

/**
 * Base collection abstraction. This class has methods appliable for all kinds
 * of collections. Some methods are yet to implemented by inheritance.
 * @package Zettacast\Collection
 * @see \Zettacast\Collection
 * @version 1.0
 */
abstract class Base implements \Countable, \Iterator, \ArrayAccess {

	/*
	 * Iterable contract inclusion. Allows all collections to be iterable by
	 * the usage of foreach. Although it is possible to change how this works.
	 */
	use Iterable;
	
	/**
	 * Data to be stored.
	 * @var array Data stored in collection.
	 */
	protected $data = [];
	
	/**
	 * Base constructor. This constructor simply sets the data received as the
	 * data stored in collection.
	 * @param array|Simple|\Traversable $data Data to be stored.
	 */
	public function __construct($data = []) {
		
		$this->data = self::convert($data);
		
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
	 * Checks whether collection is currently empty.
	 * @return bool Is collection empty?
	 */
	public function empty() {
		
		return empty($this->data);
		
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
	public function in($needle, $strict = false) {
		
		return in_array($needle, $this->data, $strict);
		
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
	 * Checks whether data can be converted into a collection.
	 * @param mixed $target Data to be checked if collectible.
	 * @return bool Is it possible data to be a collection?
	 */
	final static protected function listable($target) {
		
		return
			is_array($target) or
			$target instanceof Base or
			$target instanceof \Traversable
		;
		
	}
	
	/**
	 * Transforms given data into an array.
	 * @param mixed $target Data to be transformed into array.
	 * @return array Given data as array.
	 */
	final static protected function convert($target) {
		
		if(is_array($target))
			return $target;
		elseif($target instanceof Base)
			return $target->all();
		elseif($target instanceof \Traversable)
			return iterator_to_array($target);
		
		return (array)$target;
		
	}
	
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
	public abstract function offsetGet($offset);
	
	/**
	 * Sets data in collection using array notation.
	 * @param mixed $offset Offset to be set.
	 * @param mixed $value Data to be saved.
	 */
	public abstract function offsetSet($offset, $value);
	
	/**
	 * Erases data in collection using array notation.
	 * @param mixed $offset Offset to be erased.
	 */
	public abstract function offsetUnset($offset);
	
}
