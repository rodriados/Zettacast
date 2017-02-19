<?php
/**
 * Zettacast\Collection\ReadOnly class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

use Zettacast\Collection\Permission\Readable;

/**
 * ReadOnly collection class. This collection has constant data, so the data
 * sent by the time of instance creation cannot be changed.
 * @package Zettacast\Collection
 * @version 1.0
 */
class ReadOnly extends Base {
	
	/**
	 * Permission contract inclusions. These contracts allow simple containers
	 * to have its contents read, updated or erased.
	 */
	use Readable;
	
	/**
	 * Unlocks collection from its readonly state.
	 * @return Simple Unlocked collection.
	 */
	public function unlock() {
		
		return new Simple($this->data);
		
	}
	
	/**
	 * Sets or updates data stored using object notation.
	 * @param mixed $name Data name to be stored.
	 * @param mixed $value Value to be stored.
	 * @throws \Exception
	 */
	final public function __set($name, $value) {
		
		throw new \Exception('Readonly data cannot be updated!');
		
	}
	
	/**
	 * Erases data stored using object notation.
	 * @param mixed $name Data name to be erased.
	 * @throws \Exception
	 */
	final public function __unset($name) {
		
		throw new \Exception('Readonly data cannot be erased!');
		
	}
	
	/**
	 * Sets data in collection using array notation.
	 * @param mixed $offset Offset to be set.
	 * @param mixed $value Data to be saved.
	 * @throws \Exception
	 */
	final public function offsetSet($offset, $value) {
		
		throw new \Exception('Readonly data cannot be updated!');
		
	}
	
	/**
	 * Erases data in collection using array notation.
	 * @param mixed $offset Offset to be erased.
	 * @throws \Exception
	 */
	final public function offsetUnset($offset) {
		
		throw new \Exception('Readonly data cannot be erased!');
		
	}
	
}
