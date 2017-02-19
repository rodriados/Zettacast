<?php
/**
 * Zettacast\Collection\Dot class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

/**
 * Dot collection class. This collection implements dot access methods, that is
 * it's possible to access its recursive data via a dot string.
 * @package Zettacast\Collection
 * @version 1.0
 */
class Dot extends Recursive {
	
	/**
	 * Dot constructor. This constructor sets the data received as the data
	 * stored in collection and then recursively create new collections.
	 * @param array|Base|\Traversable $data Data to be stored.
	 */
	public function __construct($data = []) {
		
		parent::__construct($data, true);
		
	}

	/**
	 * Removes an element from collection.
	 * @param mixed $key Key to be removed.
	 */
	public function del($key) {
		$curr = $this;
		$segments = static::dot($key);
		$key = array_pop($segments);
		
		foreach($segments as $segment) {
			
			if(!$curr instanceof parent or !isset($curr->data[$segment]))
				return;
			
			$curr = &$curr->data[$segment];
			
		}
		
		unset($curr->data[$key]);
		
	}
	
	/**
	 * Creates a new collection with all elements except the specified keys.
	 * @param mixed|array $keys Keys to be forgotten in the new collection.
	 * @return static New collection instance.
	 */
	public function except($keys) {
		$keys = self::convert($keys);
		$new = new static($this);
		
		foreach($keys as $key)
			$new->del($key);
		
		return $new;
		
	}
	
	/**
	 * Gets an element stored in collection.
	 * @param mixed $key Key of requested element.
	 * @param mixed $default Default value fallback.
	 * @return mixed Requested element or default fallback.
	 */
	public function get($key, $default = null) {
		
		if(parent::has($key))
			return $this->data[$key];
		
		$curr = $this;
		
		foreach(static::dot($key) as $segment) {

			if(!$curr instanceof parent or !isset($curr->data[$segment]))
				return $default;
			
			$curr = $curr->data[$segment];
			
		}
		
		return $curr;
		
	}
	
	/**
	 * Checks whether element key exists.
	 * @param mixed $key Key to be check if exists.
	 * @return bool Does key exist?
	 */
	public function has($key) {
		
		if(parent::has($key))
			return true;

		$curr = $this;

		foreach(static::dot($key) as $segment) {
			
			if(!$curr instanceof parent or !isset($curr->data[$segment]))
				return false;
			
			$curr = $curr->data[$segment];
			
		}
		
		return true;
		
	}
	
	/**
	 * Creates a new collection with a subset of elements.
	 * @param mixed|array $keys Keys to be included in new collection.
	 * @return static New collection instance.
	 */
	public function only($keys) {
		$keys = self::convert($keys);
		$new = new static;
		
		foreach($keys as $key)
			if($this->has($key))
				$new->set($key, $this->get($key));
		
		return $new;
		
	}
	
	/**
	 * Plucks an array of values from collection.
	 * @param string|array $value Requested keys to pluck.
	 * @param string|array $key Keys to index plucked array.
	 * @return Simple The plucked values.
	 */
	public function pluck($value, $key = null) {
		$result = new Simple;
		
		foreach($this->data as $item)
			if(is_null($key))
				$result[] = $item->get($value);
			else
				$result[$item->get($key)] = $item->get($value);
			
		return $result;
		
	}
	
	/**
	 * Sets a value to the given key.
	 * @param mixed $key Key to created or updated.
	 * @param mixed $value Value to be stored in key.
	 */
	public function set($key, $value) {
		$curr = $this;
		$segments = static::dot($key);
		$key = array_pop($segments);
		
		foreach($segments as $segment) {
			
			if(!$curr instanceof parent)
				$curr = new static([$segment => new static]);
			elseif(!isset($curr->data[$segment]))
				$curr->data[$segment] = new static;
			
			$curr = &$curr->data[$segment];
			
		}
		
		$curr->data[$key] = (self::listable($value) and !$value instanceof self)
			? new static($value) : $value;
		
	}
	
	/**
	 * Explodes dot expression into array.
	 * @param mixed $key Dot expression key to be split.
	 * @return array Dot expression segments.
	 */
	protected static function dot($key) {
		
		return explode('.', $key);
		
	}
	
}
