<?php
/**
 * Zettacast\Collection\Basic class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

/**
 * Basic collection class. This class has all basic methods implemented for
 * collections. All write and read methods are available in basic collections.
 * @package Zettacast\Collection
 * @version 1.0
 */
class Basic extends Base
{
	/**
	 * Adds an element to the collection if it doesn't exist.
	 * @param mixed $key Key name to be added.
	 * @param mixed $value Value to be stored.
	 */
	public function add($key, $value)
	{
		if(!$this->has($key))
			$this->set($key, $value);
	}
	
	/**
	 * Chunks the collection into pieces of the given size.
	 * @param int $size Size of the chunks.
	 * @return static Collection of chunks.
	 */
	public function chunk($size)
	{
		if($size <= 0)
			return new static;
		
		return new static(array_chunk($this->data, $size, true) ?? []);
	}
	
	/**
	 * Removes an element from collection.
	 * @param mixed $key Key to be removed.
	 */
	public function del($key)
	{
		if($this->has($key))
			unset($this->data[$key]);
	}
	
	/**
	 * Gets items in collection that are not present in the given items.
	 * @param array $items Items to differ from.
	 * @param bool $keys Should keys be compared instead of values?
	 * @return static Diff'd array.
	 */
	public function diff($items, $keys = false)
	{
		$fn = $keys ? 'array_diff_key' : 'array_diff';
		return new static($fn($this->data, self::toarray($items)));
	}
	
	/**
	 * Divide collection's keys and values into two collections.
	 * @return static Collection of keys and collection of values.
	 */
	public function divide()
	{
		return new static([
			new static(array_keys($this->data)),
			new static(array_values($this->data))
		]);
	}
	
	/**
	 * Checks whether all elements in collection pass given test.
	 * @param callable $fn Test function. Parameters: value, key.
	 * @param bool $value Value the test function result should be compared to.
	 * @return bool Does every element pass the test?
	 */
	public function every(callable $fn = null, $value = true)
	{
		$fn = $fn ?? function ($value) { return $value; };
		
		foreach($this->iterate() as $key => $v)
			if($fn($v, $key) != $value)
				return false;
		
		return true;
	}
	
	/**
	 * Creates a new collection with all elements except the specified keys.
	 * @param mixed|array $keys Keys to be forgotten in the new collection.
	 * @return static New collection instance.
	 */
	public function except($keys)
	{
		$keys = self::toarray($keys);
		
		return $this->filter(function ($value, $key) use($keys) {
			return !in_array($key, $keys);
		});
	}
	
	/**
	 * Filters elements according to the given test. If no test function is
	 * given, it fallbacks to removing all false equivalent values.
	 * @param callable $fn Test function. Parameters: value, key.
	 * @param bool $invert Remove all values evaluated to true instead?
	 * @return static Collection of all filtered values.
	 */
	public function filter(callable $fn = null, $invert = false)
	{
		return new static(is_null($fn)
			? array_filter($this->data)
			: array_filter(
				$this->data,
				function ($v, $k) use($fn, $invert) {
					return $fn($v, $k) == !$invert;
				},
				ARRAY_FILTER_USE_BOTH
			)
		);
	}
	
	/**
	 * Gets first element in collection that passes a truth test.
	 * @param callable $fn Test function. Parameters: value, key.
	 * @param mixed $default Fallback if no element passes the test.
	 * @return mixed First element that passed the test.
	 */
	public function first(callable $fn = null, $default = null)
	{
		$fn = $fn ?: function () { return true; };
		
		foreach($this->iterate() as $key => $value)
			if($fn($value, $key))
				return $value;
		
		return $default;
	}
	
	/**
	 * Flips collection's keys and elements.
	 * @return static Flipped collection.
	 */
	public function flip()
	{
		return new static(array_flip($this->data));
	}
	
	/**
	 * Removes one or many elements from collection.
	 * @param mixed|array $keys Keys to be forgotten.
	 */
	public function forget($keys)
	{
		foreach(self::toarray($keys) as $key)
			$this->del($key);
	}
	
	/**
	 * Get an element stored in collection.
	 * @param mixed $key Key of requested element.
	 * @param mixed $default Default value fallback.
	 * @return mixed Requested element or default fallback.
	 */
	public function get($key, $default = null)
	{
		return $this->has($key) ? $this->data[$key] : $default;
	}
	
	/**
	 * Intersects given items with collection's elements.
	 * @param mixed $items Items to intersect with collection.
	 * @return static Collection of intersected elements.
	 */
	public function intersect($items)
	{
		return new static(array_intersect($this->data, self::toarray($items)));
	}
	
	/**
	 * Returns all element keys currently present in collection.
	 * @return static Collection of this collection element's keys.
	 */
	public function keys()
	{
		return new static(array_keys($this->data));
	}
	
	/**
	 * Gets last element in collection that passes a truth test.
	 * @param callable $fn Test function. Parameters: value, key.
	 * @param mixed $default Fallback if no element passes the test.
	 * @return mixed Last element that passed the test.
	 */
	public function last(callable $fn = null, $default = null)
	{
		$fn = $fn ?: function () { return true; };
		
		foreach($this->reverse()->iterate() as $key => $value)
			if($fn($value, $key))
				return $value;
		
		return $default;
	}
	
	/**
	 * Locks collection to a readonly state.
	 * @return Imutable Locked collection.
	 */
	public function lock()
	{
		return Imutable::ref($this);
	}
	
	/**
	 * Creates a new collection, the same type as the original, by using a
	 * function for creating the new elements based on the older ones. The
	 * callback receives the following parameters respectively: value, key.
	 * @param callable $fn Function to be used for creating new elements.
	 * @return static New collection instance.
	 */
	public function map(callable $fn)
	{
		$keys = array_keys($this->data);
		$values = array_map($fn, $this->data, $keys);
		
		return new static(array_combine($keys, $values));
	}
	
	/**
	 * Merges given items into collection's elements.
	 * @param mixed $items Items to be merged into collection.
	 * @return static Collection of merged elements.
	 */
	public function merge($items)
	{
		return new static(array_merge($this->data, self::toarray($items)));
	}
	
	/**
	 * Creates a new collection with a subset of elements.
	 * @param mixed|array $keys Keys to be included in new collection.
	 * @return static New collection instance.
	 */
	public function only($keys)
	{
		$keys = self::toarray($keys);
		
		return $this->filter(function ($_, $key) use($keys) {
			return in_array($key, $keys);
		});
	}
	
	/**
	 * Pops last element out of the collection and returns it.
	 * @return mixed Popped element.
	 */
	public function pop()
	{
		return array_pop($this->data);
	}
	
	/**
	 * Gets a value from collection and removes it.
	 * @param mixed $key Key to be pulled.
	 * @param mixed $default Default value if key not found.
	 * @return mixed Pulled value.
	 */
	public function pull($key, $default = null)
	{
		$value = $this->get($key, $default);
		$this->del($key);
		
		return $value;
	}
	
	/**
	 * Pushes an element onto the end of the collection.
	 * @param mixed $value Value to be pushed onto the collection.
	 * @return static Collection for method chaining.
	 */
	public function push($value)
	{
		array_push($this->data, $value);
		return $this;
	}
	
	/**
	 * Returns randomly selected elements from collection. If the amount
	 * requested is larger than the collection itself, return value will simply
	 * be shuffled collection and will not have the request amount of items.
	 * @param int $amount Amount of elements to be selected.
	 * @return static Randomly selected elements from collection.
	 */
	public function random($amount = 1)
	{
		if($amount >= $this->count())
			return $this->shuffle();
		
		$keys = array_rand($this->data, $amount);
		return new static(array_intersect_key($this->data, array_flip($keys)));
	}
	
	/**
	 * Reduces collection to a single value calculated by callback.
	 * @param callable $fn Reducing function.
	 * @param mixed $initial Initial reduction value.
	 * @return mixed Resulting value from reduction.
	 */
	public function reduce(callable $fn, $initial = null)
	{
		return array_reduce($this->data, $fn, $initial);
	}
	
	/**
	 * Replaces collection according to the given data.
	 * @param mixed $items Items to be replaced in the collection.
	 * @return static Collection with replaced data.
	 */
	public function replace($items)
	{
		return new static(array_replace($this->data, self::toarray($items)));
	}
	
	/**
	 * Reverses collection's elements order.
	 * @return static Reversed collection.
	 */
	public function reverse()
	{
		return new static(array_reverse($this->data));
	}
	
	/**
	 * Searches collection for a given value and returns successful key.
	 * @param mixed|callable $needle Value being searched or test function.
	 * @param bool $strict Should search enforce element types?
	 * @return mixed Successful key or false if none.
	 */
	public function search($needle, $strict = false)
	{
		if(is_string($needle) or !is_callable($needle))
			return array_search($needle, $this->data, $strict);
		
		foreach($this->iterate() as $key => $value)
			if($needle($value, $key))
				return $key;
		
		return false;
	}
	
	/**
	 * Sets a value to the given key.
	 * @param mixed $key Key to created or updated.
	 * @param mixed $value Value to be stored in key.
	 */
	public function set($key, $value)
	{
		$this->data[$key] = $value;
	}
	
	/**
	 * Gets and removes an element from the beginning of the collection.
	 * @return mixed Shifted value.
	 */
	public function shift()
	{
		return array_shift($this->data);
	}
	
	/**
	 * Shuffles the elements in the collection.
	 * @return static Shuffled collection.
	 */
	public function shuffle()
	{
		$data = $this->data;
		shuffle($data);
		
		return new static($data);
	}
	
	/**
	 * Gets a slice of the collection.
	 * @param int $offset Initial slice offset.
	 * @param int $length Length of requested slice.
	 * @return static Sliced collection.
	 */
	public function slice($offset, $length = null)
	{
		return new static(array_slice($this->data, $offset, $length, true));
	}
	
	/**
	 * Splits the collection into the given number of groups.
	 * @param int $count Number of groups to split the collection.
	 * @return static Splitted collection.
	 */
	public function split($count)
	{
		if($this->empty())
			return new static;
		
		return $this->chunk(ceil($this->count() / $count));
	}
	
	/**
	 * Sorts the collection using given function.
	 * @param callable $fn Ordering function.
	 * @return static Sorted collection.
	 */
	public function sort(callable $fn = null)
	{
		$data = $this->data;
		$fn ? uasort($data, $fn) : asort($data);
		
		return new static($data);
	}
	
	/**
	 * Removes part of the collection and replaces it.
	 * @param int $offset Initial splice offset.
	 * @param int $length Length of splice portion.
	 * @param array $replace Replacement for removed slice.
	 * @return static Spliced collection.
	 */
	public function splice($offset, $length = null, $replace = [])
	{
		return new static(array_splice(
			$this->data,
			$offset,
			$length ?: $this->count(),
			$replace
		));
	}
	
	/**
	 * Takes the first or last specified number of items.
	 * @param int $limit Number of items to be taken.
	 * @return static Collection of the taken items.
	 */
	public function take($limit)
	{
		return $limit < 0
			? $this->slice($limit, $this->count())
			: $this->slice(0, $limit);
	}
	
	/**
	 * Unites collection with given items.
	 * @param mixed $items Items to be united with collection.
	 * @return static United collection.
	 */
	public function union($items)
	{
		return new static($this->data + self::toarray($items));
	}
	
	/**
	 * Returns only unique items from collection.
	 * @return static Collection of unique items.
	 */
	public function unique()
	{
		return new static(array_unique($this->data, SORT_REGULAR));
	}
	
	/**
	 * Pushes an element onto the beginning of the collection.
	 * @param mixed $value Value to be prepended onto the collection.
	 * @param mixed $key Key to be used for prepended element.
	 * @return static Collection for method chaining.
	 */
	public function unshift($value, $key = null)
	{
		is_null($key)
			? array_unshift($this->data, $value)
			: $this->add($key, $value);
		
		return $this;
	}
	
	/**
	 * Returns all element values currently present in collection.
	 * @return static Collection of this collection element's values.
	 */
	public function values()
	{
		return new static(array_values($this->data));
	}
	
	/**
	 * Iterates over collection and executes a function over every element.
	 * @param callable $fn Iteration function. Parameters: value, key.
	 * @param mixed $userdata Optional third parameter for function.
	 * @return static Collection for method chaining.
	 */
	public function walk(callable $fn, $userdata = null)
	{
		foreach($this->iterate() as $key => &$value)
			$fn($value, $key, $userdata);
		
		return $this;
	}
	
	/**
	 * Zips collection together with one or more arrays.
	 * @param mixed ...$items Items to zip collection with.
	 * @return static Collection of zipped collections.
	 */
	public function zip(...$items)
	{
		$items = array_map([static::class, 'toarray'], $items);
		
		return new static(array_map(function (...$params) {
			return new static($params);
		}, $this->data, ...$items));
	}
	
}
