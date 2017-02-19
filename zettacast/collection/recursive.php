<?php
/**
 * Zettacast\Collection\Recursive class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

use Zettacast\Collection\Permission\Erasable;
use Zettacast\Collection\Permission\Readable;
use Zettacast\Collection\Permission\Writable;

/**
 * Recursive collection class. This collection implements recursive access
 * methods, that is it transforms its data into recursive collections.
 * @package Zettacast\Collection
 * @version 1.0
 */
class Recursive extends Base {
	
	/**
	 * Permission contract inclusions. These contracts allow simple containers
	 * to have its contents read, updated or erased.
	 */
	use Readable, Writable, Erasable;
	
	/**
	 * Recursive constructor. This constructor sets the data received as the
	 * data stored in collection and then recursively create new collections.
	 * @param array|Base|\Traversable $data Data to be stored.
	 * @param bool $deep Should data be deep copied to collection?
	 */
	public function __construct($data = [], $deep = false) {
		
		parent::__construct($data);
		
		foreach($this->data as &$value)
			if($value instanceof self and !$deep)
				continue;
			elseif(self::listable($value))
				$value = new static($value);
			elseif($deep and is_object($value))
				$value = clone $value;
		
	}
	
	/**
	 * Chunks the collection into pieces of the given size.
	 * @param int $size Size of the chunks.
	 * @return static Collection of chunks.
	 */
	public function chunk($size) {
		
		if($size <= 0)
			return new static;
		
		foreach(array_chunk($this->data, $size, true) as $chunk)
			$chunks[] = new static($chunk);
		
		return new static($chunks ?? []);
		
	}
	
	/**
	 * Collapses the collection into a one level shallower collection.
	 * @return static The collapsed collection.
	 */
	public function collapse() {

		return new static(array_reduce($this->data, function($carry, $value) {
			return array_merge($carry, self::convert($value));
		}, []));
		
	}
	
	/**
	 * Checks whether all elements in collection pass given test.
	 * @param callable $fn Test function. Parameters: value, key.
	 * @param bool $value Value the test function result should be compared to.
	 * @return bool Does every element pass the test?
	 */
	public function every(callable $fn = null, $value = true) {
		
		$fn = $fn ?: function($value) {
			return $value;
		};
		
		foreach($this->data as $key => $val)
			if($val instanceof self and !$val->every($fn, $value))
				return false;
			elseif($fn($val, $key) != $value)
				return false;
		
		return true;
		
	}
	
	/**
	 * Creates a new collection with all elements except the specified keys.
	 * @param mixed|array $keys Keys to be forgotten in the new collection.
	 * @return static New collection instance.
	 */
	public function except($keys) {
		$keys = self::convert($keys);
		
		return $this->filter(function($value, $key) use($keys) {
			return !in_array($key, $keys);
		});
		
	}
	
	/**
	 * Filters elements according to the given test. If no test function is
	 * given, it fallbacks to removing all false equivalent values.
	 * @param callable $fn Test function. Parameters: value, key.
	 * @return static Collection of all filtered values.
	 */
	protected function filter(callable $fn) {
		
		return new static(
			array_filter($this->data, $fn, ARRAY_FILTER_USE_BOTH)
		);
		
	}
	
	/**
	 * Flattens the recursive collection into a single level collection.
	 * @return Simple The flattened collection.
	 */
	public function flatten() {
		
		$fn = function($array) use(&$fn) {
			return array_reduce($array, function($carry, $value) use(&$fn) {
				return array_merge($carry, self::listable($value)
					? $fn(self::convert($value))
					: self::convert($value)
				);
			}, []);
		};
		
		return new Simple($fn($this->data));
		
	}
	
	/**
	 * Checks whether an element exists in collection.
	 * @param mixed $needle Element being searched for.
	 * @param bool $strict Should types be strictly the same?
	 * @return bool Was the element found?
	 */
	public function in($needle, $strict = false) {
		
		$fn = $strict
			? function($value) use($needle) { return $value === $needle; }
			: function($value) use($needle) { return $value == $needle; };
		
		return !$this->every($fn, false);
		
	}
	
	/**
	 * Returns all element keys currently present in collection.
	 * @return Simple Collection of this collection element's keys.
	 */
	public function keys() {
		
		return new Simple(array_keys($this->data));
		
	}
		
	/**
	 * Creates a new collection, the same type as the original, by using a
	 * function for creating the new elements based on the older ones. The
	 * callback receives the following parameters respectively: value, key.
	 * @param callable $fn Function to be used for creating new elements.
	 * @return static New collection instance.
	 */
	public function map(callable $fn) {
		
		$keys = array_keys($this->data);
		$values = array_map(function($value, $key) use(&$fn) {
			return $value instanceof self
				? $value->map($fn)
				: $fn($value, $key);
		}, $this->data, $keys);
		
		return new static(array_combine($keys, $values));
		
	}
	
	/**
	 * Merges given items into collection's elements.
	 * @param mixed $items Items to be merged into collection.
	 * @return static Collection of merged elements.
	 */
	public function merge($items) {
		
		return new static(array_merge($this->data, self::convert($items)));
		
	}
	
	/**
	 * Creates a new collection with a subset of elements.
	 * @param mixed|array $keys Keys to be included in new collection.
	 * @return static New collection instance.
	 */
	public function only($keys) {
		$keys = self::convert($keys);
		
		return $this->filter(function($value, $key) use($keys) {
			return in_array($key, $keys);
		});
		
	}
	
	/**
	 * Pops last element out of the collection and returns it.
	 * @return mixed Popped element.
	 */
	public function pop() {
		
		return array_pop($this->data);
		
	}
	
	/**
	 * Gets a value from collection and removes it.
	 * @param mixed $key Key to be pulled.
	 * @param mixed $default Default value if key not found.
	 * @return mixed Pulled value.
	 */
	public function pull($key, $default = null) {
		
		$value = $this->get($key, $default);
		$this->del($key);
		
		return $value;
		
	}
	
	/**
	 * Pushes an element onto the end of the collection.
	 * @param mixed $value Value to be pushed onto the collection.
	 * @return static Collection for method chaining.
	 */
	public function push($value) {
		
		array_push($this->data, $value);
		return $this;
		
	}
	
	/**
	 * Reduces collection to a single value calculated by callback.
	 * @param callable $fn Reducing function.
	 * @param mixed $initial Initial reduction value.
	 * @return mixed Resulting value from reduction.
	 */
	public function reduce(callable $fn, $initial = null) {
		
		return array_reduce($this->data, function($carry, $value) use($fn) {
			return $value instanceof self
				? $value->reduce($fn, $carry)
				: $fn($carry, $value);
		}, $initial);
		
	}
	
	/**
	 * Reverses collection's elements order.
	 * @return static Reversed collection.
	 */
	public function reverse() {
		
		return new static(array_reverse($this->data));
		
	}
		
	/**
	 * Sets a value to the given key.
	 * @param mixed $key Key to created or updated.
	 * @param mixed $value Value to be stored in key.
	 */
	public function set($key, $value) {
		
		$this->data[$key] = (self::listable($value) and !$value instanceof self)
			? new static($value) : $value;
		
	}
	
	/**
	 * Gets and removes an element from the beginning of the collection.
	 * @return mixed Shifted value.
	 */
	public function shift() {
		
		return array_shift($this->data);
		
	}
	
	/**
	 * Gets a slice of the collection.
	 * @param int $offset Initial slice offset.
	 * @param int $length Length of requested slice.
	 * @return static Sliced collection.
	 */
	public function slice($offset, $length = null) {
		
		return new static(array_slice($this->data, $offset, $length, true));
		
	}
	
	/**
	 * Splits the collection into the given number of groups.
	 * @param int $count Number of groups to split the collection.
	 * @return static Splitted collection.
	 */
	public function split($count) {
		
		if($this->empty())
			return new static;
		
		return $this->chunk(ceil($this->count() / $count));
		
	}
	
	/**
	 * Removes part of the collection and replaces it.
	 * @param int $offset Initial splice offset.
	 * @param int $length Length of splice portion.
	 * @param array $replace Replacement for removed slice.
	 * @return static Spliced collection.
	 */
	public function splice($offset, $length = null, $replace = []) {
		
		return new static(array_splice(
			$this->data, $offset, $length ?: $this->count(), $replace
		));
		
	}
	
	/**
	 * Takes the first or last specified number of items.
	 * @param int $limit Number of items to be taken.
	 * @return static Collection of the taken items.
	 */
	public function take($limit) {
		
		return $limit < 0
			? $this->slice($limit, $this->count())
			: $this->slice(0, $limit);
		
	}
	
	/**
	 * Unites collection with given items.
	 * @param mixed $items Items to be united with collection.
	 * @return static United collection.
	 */
	public function union($items) {
		
		return new static($this->data + self::convert($items));
		
	}
	
	/**
	 * Pushes an element onto the beginning of the collection.
	 * @param mixed $value Value to be prepended onto the collection.
	 * @param mixed $key Key to be used for prepended element.
	 * @return static Collection for method chaining.
	 */
	public function unshift($value, $key = null) {
		
		is_null($key)
			? array_unshift($this->data, $value)
			: $this->add($key, $value);
		
		return $this;
		
	}
	
	/**
	 * Returns all element values currently present in collection.
	 * @return Simple Collection of this collection element's values.
	 */
	public function values() {
		
		return new Simple(array_values($this->data));
		
	}
	
	/**
	 * Iterates over collection and executes a function over every element.
	 * @param callable $fn Iteration function. Parameters: value, key.
	 * @param mixed $userdata Optional third parameter for function.
	 * @return static Collection for method chaining.
	 */
	public function walk(callable $fn, &$userdata = null) {
		
		foreach($this->data as $key => &$value)
			$value instanceof self
				? $value->walk($fn, $userdata)
				: $fn($value, $key, $userdata);
		
		return $this;
		
	}
	
}
