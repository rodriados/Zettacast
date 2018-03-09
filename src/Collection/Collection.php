<?php
/**
 * Zettacast\Collection\Collection class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

use Zettacast\Helper\ArrayAccessTrait;
use Zettacast\Helper\ObjectAccessTrait;

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
	 * This constructor sets the data received as data stored in collection.
	 * @param array|\Traversable $data Data to be stored.
	 */
	public function __construct($data = null)
	{
		$this->data = !is_null($data)
			? toarray($data)
			: [];
	}
	
	/**
	 * @inheritdoc
	 */
	public function get($key, $default = null)
	{
		return $this->has($key)
			? $this->data[$key]
			: $default;
	}
	
	/**
	 * @inheritdoc
	 */
	public function has($key): bool
	{
		return isset($this->data[$key]);
	}
	
	/**
	 * @inheritdoc
	 */
	public function set($key, $value): void
	{
		if(is_null($key)) $this->data[] = $value;
		else $this->data[$key] = $value;
	}
	
	/**
	 * @inheritdoc
	 */
	public function del($key): void
	{
		if($this->has($key))
			unset($this->data[$key]);
	}
	
	/**
	 * @inheritdoc
	 */
	public function raw(): array
	{
		return toarray($this->data);
	}
	
	/**
	 * Add a group of elements to collection.
	 * @param array $values Values to add to collection.
	 * @return static Collection for method chaining.
	 */
	public function add(array $values = [])
	{
		foreach($values as $key => $value)
			$this->set($key, $value);
		
		return $this;
	}
	
	/**
	 * Apply a callback to all values stored in collection.
	 * @param callable $fn Callback to apply. Parameters: value, key.
	 * @param mixed|mixed[] $userdata Optional extra parameters for function.
	 * @return static Collection for method chaining.
	 */
	public function apply(callable $fn, $userdata = null)
	{
		foreach($this->iterate() as $key => $value)
			$this->data[$key] = $fn($value, $key, ...toarray($userdata));
		
		return $this;
	}
	
	/**
	 * Chunk the collection into pieces of given size.
	 * @param int $size Size of chunks.
	 * @return static[] Array of collection of chunks.
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
	 * @inheritdoc
	 */
	public function clear(): array
	{
		$ref = $this->data;
		$this->data = [];
		
		return $ref;
	}
	
	/**
	 * Count the number of elements currently in collection.
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
	 * Get items in collection that are not present in given items.
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
		return [$this->keys(), $this->values()];
	}
	
	/**
	 * @inheritdoc
	 */
	public function empty(): bool
	{
		return empty($this->data);
	}
	
	/**
	 * Check whether all elements in collection pass given test.
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
	 * Create a new collection with all elements except specified keys.
	 * @param mixed|mixed[] $keys Keys to forget in new collection.
	 * @return static New collection instance.
	 */
	public function except($keys)
	{
		return $this->new(
			array_diff_key($this->data, array_flip(toarray($keys)))
		);
	}
	
	/**
	 * Filter elements according to given test. If no test function is given,
	 * it fallbacks to removing all false values.
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
	 * Intersect given items with collection's elements.
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
	 * Create a generator that iterates over the collection.
	 * @yield mixed Collection's stored values.
	 */
	public function iterate(): \Generator
	{
		yield from $this->data;
	}
	
	/**
	 * Fetch key the internal pointer currently points to.
	 * @return mixed Current element's key in collection.
	 */
	public function key()
	{
		return key($this->data);
	}
	
	/**
	 * Return all element keys currently present in collection.
	 * @return self Collection of this collection's element's keys.
	 */
	public function keys(): self
	{
		return new self(array_keys($this->data));
	}
	
	/**
	 * Create a new collection, the same type as original, by using a function
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
	 * Merge given items with this collection's elements.
	 * @param mixed $items Items to merge with collection.
	 * @return static Collection of merged elements.
	 */
	public function merge($items)
	{
		return $this->new(array_merge($this->data, toarray($items)));
	}
	
	/**
	 * Advance the internal pointer one position.
	 * @return mixed Element in the next position.
	 */
	public function next()
	{
		return next($this->data);
	}
	
	/**
	 * Create a new collection with a subset of elements.
	 * @param mixed|mixed[] $keys Keys to include in new collection.
	 * @return static New collection instance.
	 */
	public function only($keys)
	{
		return $this->new(
			array_intersect_key($this->data, array_flip(toarray($keys)))
		);
	}
	
	/**
	 * Pass the collection to given function and returns its result.
	 * @param callable $fn Function to which collection is passed to.
	 * @return mixed Callback's return result.
	 */
	public function pipe(callable $fn)
	{
		return $fn($this);
	}
	
	/**
	 * Rewind the internal pointer one position.
	 * @return mixed Element in the previous position.
	 */
	public function prev()
	{
		return prev($this->data);
	}
	
	/**
	 * Get a value from collection and remove it.
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
	 * Return randomly selected elements from collection. If the amount
	 * requested is larger than the collection itself, return value will be
	 * the collection shuffled and will not have the requested amount of items.
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
	 * Reduce collection to a single value calculated by callback.
	 * @param callable $fn Reducing function. Parameters: carry, value.
	 * @param mixed $initial Initial reduction value.
	 * @return mixed Resulting value from reduction.
	 */
	public function reduce(callable $fn, $initial = null)
	{
		return array_reduce($this->data, $fn, $initial);
	}
	
	/**
	 * Replace collection according to given data.
	 * @param mixed $items Items to replace in collection.
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
	 * Search collection for given value and return successful key.
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
	 * Shuffle the elements in collection.
	 * @return static Shuffled collection.
	 */
	public function shuffle()
	{
		$data = $this->data;
		shuffle($data);
		
		return $this->new($data);
	}
	
	/**
	 * Split collection into given number of groups.
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
	 * Pass collection to given function and return it.
	 * @param callable $fn Function to which collection is passed to.
	 * @return static Collection's copy sent to function.
	 */
	public function tap(callable $fn)
	{
		$fn($copy = clone $this);
		return $copy;
	}
	
	/**
	 * Unify collection with given items.
	 * @param mixed $items Items to unify with collection.
	 * @return static United collection.
	 */
	public function union($items)
	{
		return $this->new($this->data + toarray($items));
	}
	
	/**
	 * Return only unique items from collection.
	 * @return static Collection of unique items.
	 */
	public function unique()
	{
		return $this->new(array_unique($this->data, SORT_REGULAR));
	}
	
	/**
	 * Check whether the pointer is a valid position.
	 * @return bool Is the pointer in a valid position?
	 */
	public function valid(): bool
	{
		return key($this->data) !== null;
	}
	
	/**
	 * Return all element values currently present in collection.
	 * @return self Collection of this collection element's values.
	 */
	public function values(): self
	{
		return new self(array_values($this->data));
	}
	
	/**
	 * Iterate over collection and execute a function over every element.
	 * @param callable $fn Iteration function. Parameters: value, key.
	 * @param mixed|mixed[] $userdata Optional extra parameters for function.
	 * @return static Collection for method chaining.
	 */
	public function walk(callable $fn, $userdata = null)
	{
		foreach($this->iterate() as $key => $value)
			$fn($value, $key, ...toarray($userdata));
		
		return $this;
	}
	
	/**
	 * Zip collection together with one or more arrays.
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
	 * Create a new instance of class based on an already existing instance.
	 * @param mixed $target Data to feed into new instance.
	 * @return static The new instance.
	 */
	protected function new($target = [])
	{
		$obj = new static($target);
		return $obj;
	}
	
	/**
	 * Create a new instance of class based on an already existing instance,
	 * and using by-reference assignment to data stored in instance.
	 * @param mixed &$target Data to feed into new instance.
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
