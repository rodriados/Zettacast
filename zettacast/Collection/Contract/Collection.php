<?php
/**
 * Zettacast\Collection\Contract\Collection interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Collection\Contract;

use Countable;

interface Collection extends Countable
{
	/**
	 * Collection constructor. This constructor simply sets the data received
	 * as the data stored in collection.
	 * @param array|Collection|\Traversable $data Data to be stored.
	 */
	public function __construct($data = null);
	
	/**
	 * Returns all data stored in collection.
	 * @return array All data stored in collection.
	 */
	public function all();
	
	/**
	 * Counts the number of elements currently in collection.
	 * @return int Number of elements stored in the collection.
	 */
	public function count();
		
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
	 * Sets a value to the given key.
	 * @param mixed $key Key to created or updated.
	 * @param mixed $value Value to be stored in key.
	 */
	public function set($key, $value);
	
}
