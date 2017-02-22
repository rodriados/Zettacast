<?php
/**
 * Zettacast\Collection\Imutable class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

/**
 * Imutable collection class. This collection has constant data, so the data
 * sent by the time of instance creation cannot be changed.
 * @package Zettacast\Collection
 * @version 1.0
 */
class Imutable extends Base {
	
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
	 * Sets or updates data stored using object notation.
	 * @param mixed $name Data name to be stored.
	 * @param mixed $value Value to be stored.
	 * @throws \Exception
	 */
	final public function set($name, $value) {
		
		throw new \Exception('Readonly data cannot be updated!');
		
	}
	
	/**
	 * Erases data stored using object notation.
	 * @param mixed $name Data name to be erased.
	 * @throws \Exception
	 */
	final public function del($name) {
		
		throw new \Exception('Readonly data cannot be erased!');
		
	}
	
}
