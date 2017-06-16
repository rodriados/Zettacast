<?php
/**
 * Zettacast\Collection\Secure class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

use Traversable;
use Zettacast\Collection\Contract\Collection;

/**
 * Secure collection. This class has methods that keeps its data stored in
 * a more secure way than common collections.
 * @package Zettacast\Collection
 * @see \Zettacast\Collection
 * @version 1.0
 */
final class Secure implements Collection {
	
	/**
	 * Key for data stored in collection.
	 * @var string Hash key for data storage.
	 */
	private $hash;
	
	/**
	 * Data to be stored.
	 * @var array Data stored in collection.
	 */
	private static $data = [];
	
	/**
	 * Secure constructor. This constructor simply securely sets the data
	 * received as the data stored in collection.
	 * @param array|Collection|\Traversable $data Data to be stored.
	 */
	public function __construct($data = null) {
		
		$this->hash = spl_object_hash($this);
		self::$data[$this->hash] = [];
		
		foreach(self::toarray($data) as $key => $value)
			self::$data[$this->hash][$key] = $value;
		
	}
	
	/**
	 * Destroys this object and deletes all data stored in it. This will leave
	 * no trace of the data that was once stored here.
	 */
	public function __destruct() {
		
		unset(static::$data[$this->hash]);
		
	}
	
	/**
	 * Returns all data stored in collection.
	 * @return array All data stored in collection.
	 */
	public function all() {
		
		return self::$data[$this->hash];
		
	}
	
	/**
	 * Counts the number of elements currently in collection.
	 * @return int Number of elements stored in the collection.
	 */
	public function count() {
		
		return count(self::$data[$this->hash]);
		
	}
	
	/**
	 * Removes an element from collection.
	 * @param mixed $key Key to be removed.
	 */
	public function del($key) {
		
		if($this->has($key))
			unset(self::$data[$this->hash][$key]);
		
	}
	
	/**
	 * Checks whether collection is currently empty.
	 * @return bool Is collection empty?
	 */
	public function empty() {
		
		return empty(self::$data[$this->hash]);
		
	}
	
	/**
	 * Get an element stored in collection.
	 * @param mixed $key Key of requested element.
	 * @param mixed $default Default value fallback.
	 * @return mixed Requested element or default fallback.
	 */
	public function get($key, $default = null) {
		
		return $this->has($key)
			? self::$data[$this->hash][$key]
			: $default;
		
	}
	
	/**
	 * Checks whether element key exists.
	 * @param mixed $key Key to be check if exists.
	 * @return bool Does key exist?
	 */
	public function has($key) {
		
		return array_key_exists($key, self::$data[$this->hash]);
		
	}
	
	/**
	 * Sets a value to the given key.
	 * @param mixed $key Key to created or updated.
	 * @param mixed $value Value to be stored in key.
	 */
	public function set($key, $value) {
		
		self::$data[$this->hash][$key] = $value;
		
	}
	
	/**
	 * Transforms given data into an array.
	 * @param mixed $data Data to be transformed into array.
	 * @return array Given data as array.
	 */
	final static private function toarray($data) {
		
		if(is_array($data)) return $data;
		elseif($data instanceof Collection) return $data->all();
		elseif($data instanceof Traversable) return iterator_to_array($data);
		
		return [$data];
		
	}
	
}
