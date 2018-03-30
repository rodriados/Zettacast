<?php
/**
 * Zettacast\Collection\Collection class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

use Zettacast\Support\ArrayAccessTrait;
use Zettacast\Support\ObjectAccessTrait;

/**
 * The collection class. This class has methods appliable for all kinds of
 * collections. Only scalar key types, such as strings and integers, are
 * acceptable in all kinds of collection.
 * @package Zettacast\Collection
 * @version 1.0
 */
class Collection implements CollectionInterface, \ArrayAccess
{
	use ArrayAccessTrait;
	use ObjectAccessTrait;
	
	/**
	 * Data to be stored.
	 * @var array Data stored in collection.
	 */
	protected $data = [];
	
	/**
	 * Collection constructor.
	 * Sets given data as the data stored by collection.
	 * @param mixed $data Data to be stored.
	 */
	public function __construct($data = null)
	{
		$this->data = !is_null($data)
			? toarray($data)
			: [];
	}
	
	/**
	 * Gets an element stored in collection.
	 * @param mixed $key Key of requested element.
	 * @param mixed $default Default value fallback.
	 * @return mixed Requested element or default fallback.
	 */
	public function get($key, $default = null)
	{
		return $this->has($key)
			? $this->data[$key]
			: $default;
	}
	
	/**
	 * Checks whether element key exists.
	 * @param mixed $key Key to check existance.
	 * @return bool Does key exist?
	 */
	public function has($key): bool
	{
		return isset($this->data[$key]);
	}
	
	/**
	 * Sets a value to given key.
	 * @param mixed $key Key to create or update.
	 * @param mixed $value Value to store in key.
	 */
	public function set($key, $value): void
	{
		if(is_null($key)) $this->data[] = $value;
		else $this->data[$key] = $value;
	}
	
	/**
	 * Removes an element from collection.
	 * @param mixed $key Key to remove.
	 */
	public function del($key): void
	{
		if($this->has($key))
			unset($this->data[$key]);
	}
	
	/**
	 * Returns all data stored in collection.
	 * @return array All data stored in collection.
	 */
	public function raw(): array
	{
		return toarray($this->data);
	}
	
	/**
	 * Adds a group of elements to collection.
	 * @param iterable $values Values to add to collection.
	 */
	public function add(iterable $values = []): void
	{
		foreach($values as $key => $value)
			$this->set($key, $value);
	}
	
	/**
	 * Applies a callback to all values stored in collection.
	 * @param callable $fn Callback to apply. Parameters: value, key.
	 * @param mixed $userdata Optional extra parameters for function.
	 * @return static The current collection for method chaining.
	 */
	public function apply(callable $fn, $userdata = null)
	{
		$userdata = toarray($userdata);
		
		foreach($this->iterate() as $key => $value)
			$this->data[$key] = $fn($value, $key, ...$userdata);
		
		return $this;
	}
	
	/**
	 * Chunks the collection into pieces of given size.
	 * @param int $size Size of chunks.
	 * @return static Collection of chunked collections.
	 */
	public function chunk(int $size)
	{
		if($size <= 0)
			return $this->new();
		
		foreach(array_chunk($this->data, $size, true) as $collection)
			$chunk[] = $this->new($collection);
		
		return $this->new($chunk ?? []);
	}
	
	/**
	 * Clears all data stored in object and returns it.
	 * @return array All data stored in collection before clearing.
	 */
	public function clear(): array
	{
		$ref = $this->data;
		$this->data = [];
		
		return $ref;
	}
	
	/**
	 * Counts the number of elements currently in collection.
	 * @return int Number of elements stored in collection.
	 */
	public function count(): int
	{
		return count($this->data);
	}
	
	/**
	 * Return the element the internal pointer currently points to.
	 * @return mixed Current element in collection.
	 */
	public function current()
	{
		return current($this->data);
	}
	
	/**
	 * Gets items in collection that are not present in given items.
	 * @param iterable $items Items to differ from.
	 * @param bool $keys Should keys also be compared?
	 * @return static The diff'd collection.
	 */
	public function diff(iterable $items, bool $keys = false)
	{
		$fn = $keys ? 'array_diff_assoc' : 'array_diff';
		return $this->new($fn($this->data, toarray($items)));
	}
	
	/**
	 * Divide collection's keys and values into two collections.
	 * @return static Collection of keys and values.
	 */
	public function divide()
	{
		return $this->new([$this->keys(), $this->values()]);
	}
	
	/**
	 * Checks whether collection is currently empty.
	 * @return bool Is collection empty?
	 */
	public function empty(): bool
	{
		return empty($this->data);
	}
	
	/**
	 * Checks whether all elements in collection pass given test.
	 * @param callable $fn Test function. Parameters: value, key.
	 * @return bool Does every element pass the tests?
	 */
	public function every(callable $fn = null): bool
	{
		$fn = $fn ?: 'with';
		
		foreach($this->iterate() as $key => $value)
			if(!$fn($value, $key))
				return false;
		
		return true;
	}
	
	/**
	 * Creates a new collection with all elements except specified keys.
	 * @param mixed|array $keys Keys to forget in new collection.
	 * @return static New collection instance.
	 */
	public function except($keys)
	{
		return $this->new(
			array_diff_key($this->data, array_flip(toarray($keys)))
		);
	}
	
	/**
	 * Filters elements according to given test. If no test function is
	 * given, it fallbacks to removing all false values.
	 * @param callable $fn Test function. Parameters: value, key.
	 * @return static Collection of all filtered values.
	 */
	public function filter(callable $fn = null)
	{
		return $this->new(is_null($fn)
			? array_filter($this->data)
			: array_filter($this->data, $fn, ARRAY_FILTER_USE_BOTH)
		);
	}
	
	/**
	 * Intersects given items with collection's elements.
	 * @param iterable $items Items to intersect with collection.
	 * @param bool $keys Should keys also be compared?
	 * @return static Collection of intersected elements.
	 */
	public function intersect(iterable $items, bool $keys = false)
	{
		$fn = $keys ? 'array_intersect_assoc' : 'array_intersect';
		return $this->new($fn($this->data, toarray($items)));
	}
	
	/**
	 * Create a generator that iterates over collection.
	 * @yield mixed Collection's stored values.
	 */
	public function iterate(): \Generator
	{
		yield from $this->data;
	}
	
	/**
	 * Fetches key the internal pointer currently points to.
	 * @return mixed Current element's key in collection.
	 */
	public function key()
	{
		return key($this->data);
	}
	
	/**
	 * Returns all element keys currently present in collection.
	 * @return static Collection of this collection element's keys.
	 */
	public function keys()
	{
		return $this->new(array_keys($this->data));
	}
	
	/**
	 * Creates a new collection, the same type as original, by using a function
	 * for creating the new elements based on the older ones. The callback
	 * receives the following parameters respectively: value, key.
	 * @param callable $fn Function to use for creating new elements.
	 * @return static New collection instance.
	 */
	public function map(callable $fn)
	{
		$keys = array_keys($this->data);
		$values = array_map($fn, $this->data, $keys);
		
		return $this->new(array_combine($keys, $values));
	}
	
	/**
	 * Merges given items with this collection's elements.
	 * @param iterable $items Items to merge with collection.
	 * @return static Collection of merged elements.
	 */
	public function merge(iterable $items)
	{
		return $this->new(array_merge($this->data, toarray($items)));
	}
	
	/**
	 * Advances the internal pointer one position.
	 * @return mixed Element in the next position.
	 */
	public function next()
	{
		return next($this->data);
	}
	
	/**
	 * Creates a new collection with a subset of elements.
	 * @param mixed|array $keys Keys to include in new collection.
	 * @return static New collection instance.
	 */
	public function only($keys)
	{
		return $this->new(
			array_intersect_key($this->data, array_flip(toarray($keys)))
		);
	}
	
	/**
	 * Passes the collection to given function and returns its result.
	 * @param callable $fn Function to which collection is passed to.
	 * @return mixed Callback's return result.
	 */
	public function pipe(callable $fn)
	{
		return $fn($this);
	}
	
	/**
	 * Rewinds the internal pointer one position.
	 * @return mixed Element in the previous position.
	 */
	public function prev()
	{
		return prev($this->data);
	}
	
	/**
	 * Gets a value from collection and removes it.
	 * @param mixed $key Key to pull.
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
	 * Returns randomly selected elements from collection. If the amount
	 * requested is larger than the collection itself, return value will be
	 * the collection shuffled and will not have the requested amount of items.
	 * @param int $sample Amount of elements to be selected.
	 * @return static Randomly selected elements from collection.
	 */
	public function random(int $sample = 1)
	{
		if($sample >= $this->count())
			return $this->shuffle();
		
		return $this->only(array_rand($this->data, $sample));
	}
	
	/**
	 * Reduces collection to a single value calculated by callback.
	 * @param callable $fn Reducing function. Parameters: carry, value.
	 * @param mixed $initial Initial reduction value.
	 * @return mixed Resulting value from reduction.
	 */
	public function reduce(callable $fn, $initial = null)
	{
		return array_reduce($this->data, $fn, $initial);
	}
	
	/**
	 * Replaces collection's items according to given data.
	 * @param iterable $items Items to replace in collection.
	 * @return static Collection with replaced data.
	 */
	public function replace(iterable $items)
	{
		return $this->new(array_replace($this->data, toarray($items)));
	}
	
	/**
	 * Sets the internal pointer of the collection to its initial element.
	 * @return mixed Initial element in collection.
	 */
	public function rewind()
	{
		return reset($this->data);
	}
	
	/**
	 * Searches collection for given value and returns the successful key.
	 * @param mixed|callable $needle Value to search or test function.
	 * @param bool $strict Should search enforce strict element types?
	 * @return mixed|bool Successful key or false if none.
	 */
	public function search($needle, bool $strict = false)
	{
		if(!is_callable($needle))
			return array_search($needle, $this->data, $strict);
		
		foreach($this->iterate() as $key => $value)
			if($needle($value, $key))
				return $key;
		
		return false;
	}
	
	/**
	 * Shuffles the elements in collection.
	 * @return static Shuffled collection.
	 */
	public function shuffle()
	{
		$copy = clone $this;
		shuffle($copy->data);
		
		return $copy;
	}
	
	/**
	 * Splits collection into given number of groups.
	 * @param int $count Number of groups to split the collection.
	 * @return static Collection of splitted collections.
	 */
	public function split(int $count)
	{
		return !$this->empty()
			? $this->chunk(ceil($this->count() / (float)$count))
			: $this->new();
	}
	
	/**
	 * Passes collection to given function and returns it.
	 * @param callable $fn Function to which collection is passed to.
	 * @return static Collection's copy sent to function.
	 */
	public function tap(callable $fn)
	{
		$fn($copy = clone $this);
		return $copy;
	}
	
	/**
	 * Unions collection with given items.
	 * @param iterable $items Items to union with collection.
	 * @return static United collection.
	 */
	public function union(iterable $items)
	{
		return $this->new($this->data + toarray($items));
	}
	
	/**
	 * Returns only unique items from collection.
	 * @return static Collection of unique items.
	 */
	public function unique()
	{
		return $this->new(array_unique($this->data, SORT_REGULAR));
	}
	
	/**
	 * Checks whether the pointer is a valid position.
	 * @return bool Is the pointer in a valid position?
	 */
	public function valid(): bool
	{
		return key($this->data) !== null;
	}
	
	/**
	 * Returns all element values currently present in collection.
	 * @return static Collection of this collection element's values.
	 */
	public function values()
	{
		return $this->new(array_values($this->data));
	}
	
	/**
	 * Iterates over collection and executes a function over every element.
	 * @param callable $fn Iteration function. Parameters: value, key.
	 * @param mixed $userdata Optional extra parameters for function.
	 * @return static The current collection for method chaining.
	 */
	public function walk(callable $fn, $userdata = null)
	{
		$userdata = toarray($userdata);
		
		foreach($this->iterate() as $key => $value)
			$fn($value, $key, ...$userdata);
		
		return $this;
	}
	
	/**
	 * Zips collection together with one or more arrays.
	 * @param iterable[] $items Items to zip collection with.
	 * @return static Collection of zipped collections.
	 */
	public function zip(iterable ...$items)
	{
		$items = array_map('toarray', $items);
		
		return $this->new(array_map(function(...$params) {
				return $this->new($params);
			}, $this->data, ...$items));
	}
	
	/**
	 * Creates a new instance of class based on an already existing instance,
	 * and using by-reference assignment to data stored in instance.
	 * @param mixed &$target Data to feed into new instance.
	 * @param Collection $base Instance to use as a base for new ref instance.
	 * @return static|mixed The new instance or original data.
	 */
	public static function ref(&$target, Collection $base = null)
	{
		if(!is_array($target) and !$target instanceof \ArrayAccess)
			return $target;
		
		$obj = $base ? $base->new() : new static();
		$obj->data = &$target;
		return $obj;
	}
	
	/**
	 * Creates a new instance of class based on an already existing instance.
	 * @param mixed $target Data to feed into new instance.
	 * @return static The new instance.
	 */
	protected function new($target = [])
	{
		$obj = new static($target);
		return $obj;
	}
}
