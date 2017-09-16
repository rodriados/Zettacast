<?php
/**
 * Zettacast\Collection\Collection class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

use Zettacast\Collection\Concerns\ArrayAccessTrait;
use Zettacast\Collection\Concerns\ObjectAccessTrait;

/**
 * Collection class. This class has methods appliable for all kinds of
 * collections. Only scalar key types, such as string and int, are acceptable.
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
	 * Collection constructor. This constructor simply sets the data received
	 * as the data stored in collection.
	 * @param array|\Traversable $data Data to be stored.
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
	 * @param mixed $key Key to be checked if exists.
	 * @return bool Does key exist?
	 */
	public function has($key): bool
	{
		return isset($this->data[$key]);
	}
	
	/**
	 * Sets a value to the given key.
	 * @param mixed $key Key to be created or updated.
	 * @param mixed $value Value to be stored in key.
	 * @return $this Collection for method chaining.
	 */
	public function set($key, $value)
	{
		if(is_null($key)) $this->data[] = $value;
		else $this->data[$key] = $value;

		return $this;
	}
	
	/**
	 * Removes an element from collection.
	 * @param mixed $key Key to be removed.
	 * @return $this Collection for method chaining.
	 */
	public function del($key)
	{
		if($this->has($key))
			unset($this->data[$key]);
		
		return $this;
	}
	
	/**
	 * Adds a group of elements to the collection.
	 * @param array $values Values to be added to collection.
	 * @return $this Collection for method chaining.
	 */
	public function add(array $values = [])
	{
		foreach($values as $key => $value)
			$this->set($key, $value);
		
		return $this;
	}
	
	/**
	 * Returns all data stored in collection.
	 * @return array All data stored in collection.
	 */
	public function all(): array
	{
		return $this->data;
	}
	
	/**
	 * Applies a callback to all values stored in collection.
	 * @param callable $fn Callback to be applied. Parameters: value, key.
	 * @param mixed|mixed[] $userdata Optional extra parameters for function.
	 * @return $this Collection for method chaining.
	 */
	public function apply(callable $fn, $userdata = null)
	{
		$userdata = toarray($userdata);
		
		foreach($this->iterate() as $key => $value)
			$this->data[$key] = $fn($value, $key, ...$userdata);
		
		return $this;
	}
	
	/**
	 * Chunks the collection into pieces of the given size.
	 * @param int $size Size of the chunks.
	 * @return array Array of collection of chunks.
	 */
	public function chunk(int $size): array
	{
		if($size <= 0)
			return [];
		
		foreach(array_chunk($this->data, $size, true) as $collection)
			$chunk[] = $this->new($collection);
		
		return $chunk ?? [];
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
	 * Copies all the content present in this object.
	 * @return static A new collection with copied data.
	 */
	public function copy()
	{
		return clone $this;
	}
	
	/**
	 * Counts the number of elements currently in collection.
	 * @return int Number of elements stored in the collection.
	 */
	public function count(): int
	{
		return count($this->data);
	}
	
	/**
	 * Return the element the internal pointer currently points to.
	 * @return mixed Current element in the collection.
	 */
	public function current()
	{
		return current($this->data);
	}
	
	/**
	 * Gets items in collection that are not present in the given items.
	 * @param array|Collection $items Items to differ from.
	 * @param bool $keys Should keys also be compared?
	 * @return static Diff'd array.
	 */
	public function diff($items, bool $keys = false)
	{
		$fn = $keys ? 'array_diff_assoc' : 'array_diff';
		return $this->new($fn($this->data, toarray($items)));
	}
	
	/**
	 * Divide collection's keys and values into two collections.
	 * @return array Array of collections of keys and values.
	 */
	public function divide(): array
	{
		return [
			$this->keys(),
			$this->values()
		];
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
	 * Creates a new collection with all elements except the specified keys.
	 * @param mixed|mixed[] $keys Keys to be forgotten in the new collection.
	 * @return static New collection instance.
	 */
	public function except($keys)
	{
		return $this->new(
			array_diff_key($this->data, array_flip(toarray($keys)))
		);
	}
	
	/**
	 * Filters elements according to the given test. If no test function is
	 * given, it fallbacks to removing all false equivalent values.
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
	 * @param array|Collection $items Items to intersect with collection.
	 * @param bool $keys Should keys also be compared?
	 * @return static Collection of intersected elements.
	 */
	public function intersect($items, bool $keys = false)
	{
		$fn = $keys ? 'array_intersect_assoc' : 'array_intersect';
		return $this->new($fn($this->data, toarray($items)));
	}
	
	/**
	 * Creates a generator that iterates over the collection.
	 * @yield mixed Collection's stored values.
	 */
	public function iterate(): \Generator
	{
		yield from $this->data;
	}
	
	/**
	 * Fetches the key the internal pointer currently points to.
	 * @return mixed Current element's key in the collection.
	 */
	public function key()
	{
		return key($this->data);
	}
	
	/**
	 * Returns all element keys currently present in collection.
	 * @return self Collection of this collection element's keys.
	 */
	public function keys(): self
	{
		return new self(array_keys($this->data));
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
		return $this->new(array_combine($keys, $values));
	}
	
	/**
	 * Merges given items with this collection's elements.
	 * @param mixed $items Items to be merged with collection.
	 * @return static Collection of merged elements.
	 */
	public function merge($items)
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
	 * @param mixed|mixed[] $keys Keys to be included in new collection.
	 * @return static New collection instance.
	 */
	public function only($keys)
	{
		return $this->new(
			array_intersect_key($this->data, array_flip(toarray($keys)))
		);
	}
	
	/**
	 * Passes the collection to the given function and returns the result.
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
	 * Returns randomly selected elements from collection. If the amount
	 * requested is larger than the collection itself, return value will simply
	 * be shuffled collection and will not have the requested amount of items.
	 * @param int $sample Amount of elements to be selected.
	 * @return static Randomly selected elements from collection.
	 */
	public function random(int $sample = 1)
	{
		if($sample >= $this->count())
			return $this->shuffle();
		
		$keys = array_rand($this->data, $sample);
		return $this->only($keys);
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
	 * Replaces collection according to the given data.
	 * @param mixed $items Items to be replaced in the collection.
	 * @return static Collection with replaced data.
	 */
	public function replace($items)
	{
		return $this->new(array_replace($this->data, toarray($items)));
	}
	
	/**
	 * Set the internal pointer of the collection to its first element.
	 * @return mixed First element in collection.
	 */
	public function rewind()
	{
		return reset($this->data);
	}
	
	/**
	 * Searches collection for a given value and returns successful key.
	 * @param mixed|callable $needle Value being searched or test function.
	 * @param bool $strict Should search enforce element types?
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
	 * Shuffles the elements in the collection.
	 * @return static Shuffled collection.
	 */
	public function shuffle()
	{
		$data = $this->data;
		shuffle($data);
		
		return $this->new($data);
	}
	
	/**
	 * Splits the collection into the given number of groups.
	 * @param int $count Number of groups to split the collection.
	 * @return array Splitted collection.
	 */
	public function split(int $count): array
	{
		return !$this->empty()
			? $this->chunk(ceil($this->count() / (float)$count))
			: [];
	}
	
	/**
	 * Passes the collection to the given function and returns it.
	 * @param callable $fn Function to which collection is passed to.
	 * @return static Collection's copy sent to function.
	 */
	public function tap(callable $fn)
	{
		$fn($copy = $this->copy());
		return $copy;
	}
	
	/**
	 * Unites collection with given items.
	 * @param mixed $items Items to be united with collection.
	 * @return static United collection.
	 */
	public function union($items)
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
	 * @return self Collection of this collection element's values.
	 */
	public function values(): self
	{
		return new self(array_values($this->data));
	}
	
	/**
	 * Iterates over collection and executes a function over every element.
	 * @param callable $fn Iteration function. Parameters: value, key.
	 * @param mixed|mixed[] $userdata Optional extra parameters for function.
	 * @return $this Collection for method chaining.
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
	 * @param mixed[] ...$items Items to zip collection with.
	 * @return array Array of zipped collections.
	 */
	public function zip(...$items): array
	{
		$items = array_map('toarray', $items);
		
		return array_map(function(...$params) {
			return $this->new($params);
		}, $this->data, ...$items);
	}
	
	/**
	 * Creates a new instance of class based on an already existing instance.
	 * @param mixed $target Data to be fed into the new instance.
	 * @return static The new instance.
	 */
	protected function new($target = [])
	{
		$obj = new static($target);
		return $obj;
	}
	
	/**
	 * Creates a new instance of class based on an already existing instance,
	 * and using by-reference assignment to data stored in new instance.
	 * @param mixed &$target Data to be fed into the new instance.
	 * @return static The new instance.
	 */
	protected function ref(&$target)
	{
		if(!listable($target))
			return $target;
		
		$obj = $this->new();
		$obj->data = &$target;
		
		return $obj;
	}
	
}
