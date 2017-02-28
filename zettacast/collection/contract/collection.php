<?php
/**
 * Collection\Contract\Collection interface class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Collection\Contract;

use Iterator;
use Countable;
use ArrayAccess;

interface Collection extends Iterator, Countable, ArrayAccess {
	
	/**
	 * Collection constructor. This constructor simply sets the data received
	 * as the data stored in collection.
	 * @param array|Collection|\Traversable $data Data to be stored.
	 */
	public function __construct($data = null);
	
	/**
	 * Allows access to data using object notation.
	 * @param mixed $name Data to be accessed in collection.
	 * @return mixed Accessed data.
	 */
	public function __get($name);
	
	/**
	 * Checks whether data exists using object notation.
	 * @param mixed $name Data name to be checked existence.
	 * @return bool Is offset set?
	 */
	public function __isset($name);
	
	/**
	 * Sets or updates data stored using object notation.
	 * @param mixed $name Data name to be stored.
	 * @param mixed $value Value to be stored.
	 */
	public function __set($name, $value);
	
	/**
	 * Erases data stored using object notation.
	 * @param mixed $name Data name to be erased.
	 */
	public function __unset($name);
	
	/**
	 * Returns all data stored in collection.
	 * @return array All data stored in collection.
	 */
	public function all();
	
	/**
	 * Creates a copy of collection.
	 * @return static Copied collection.
	 */
	public function copy();
	
	/**
	 * Removes an element from collection.
	 * @param mixed $key Key to be removed.
	 */
	public function del($key);
	
	/**
	 * Checks whether collection is currently empty.
	 * @return bool Is collection empty?
	 */
	public function empty();
	
	/**
	 * Get an element stored in collection.
	 * @param mixed $key Key of requested element.
	 * @param mixed $default Default value fallback.
	 * @return mixed Requested element or default fallback.
	 */
	public function get($key, $default = null);
	
	/**
	 * Checks whether element key exists.
	 * @param mixed $key Key to be check if exists.
	 * @return bool Does key exist?
	 */
	public function has($key);
	
	/**
	 * Checks whether an element exists in collection.
	 * @param mixed $needle Element being searched for.
	 * @param bool $strict Should types be strictly the same?
	 * @return bool Was the element found?
	 */
	public function in($needle, bool $strict);
	
	/**
	 * Creates a generator that iterates over the collection.
	 * @yield mixed Collection's stored values.
	 */
	public function iterate();
	
	/**
	 * Fetches the key the internal pointer currently points to.
	 * @return mixed Current element's key in the collection.
	 */
	public function key();
	
	/**
	 * Sets a value to the given key.
	 * @param mixed $key Key to created or updated.
	 * @param mixed $value Value to be stored in key.
	 */
	public function set($key, $value);
	
}
