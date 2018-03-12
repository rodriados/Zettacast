<?php
/**
 * Zettacast\Collection\RecursiveCollection class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

/**
 * The recursive collection class has methods appliable for all kinds of
 * recursive collections. Only scalar key types, such as strings and integers,
 * are acceptable in collections.
 * @package Zettacast\Collection
 * @version 1.0
 */
class RecursiveCollection extends Collection
{
	/**
	 * Gets an element stored in collection.
	 * @param mixed $key Key of requested element.
	 * @param mixed $default Default value fallback.
	 * @return mixed Requested element or default fallback.
	 */
	public function get($key, $default = null)
	{
		return $this->has($key)
			? $this->ref($this->data[$key])
			: $default;
	}
	
	/**
	 * Applies a callback to all values stored in collection.
	 * @param callable $fn Callback to apply. Parameters: value, key.
	 * @param mixed|mixed[] $userdata Optional extra parameters for function.
	 * @return static Collection for method chaining.
	 */
	public function apply(callable $fn, $userdata = null)
	{
		foreach(parent::iterate() as $key => $value)
			$this->data[$key] = listable($value)
				? $this->new($value)->apply($fn, ...toarray($userdata))
				: $fn($value, $key, ...toarray($userdata));
		
		return $this;
	}
	
	/**
	 * Collapses collection into a one level shallower collection.
	 * @return static The collapsed collection.
	 */
	public function collapse()
	{
		return $this->new(
			array_reduce($this->data, function($carry, $value) {
				return array_merge($carry, toarray($value));
			}, [])
		);
	}
	
	/**
	 * Filters elements according to given test. If no test function is given,
	 * it fallbacks to removing all false values.
	 * @param callable $fn Test function. Parameters: value, key.
	 * @return static Collection of all filtered values.
	 */
	public function filter(callable $fn = null)
	{
		$fn = $fn ?? 'with';
		
		foreach(parent::iterate() as $key => $value)
			if($fn($value, $key))
				$result[$key] = listable($value)
					? $this->new($value)->filter($fn)
					: $value;
		
		return $this->new($result ?? []);
	}
	
	/**
	 * Flattens collection into a single level collection.
	 * @return Collection The flattened collection.
	 */
	public function flatten(): Collection
	{
		foreach($this->iterate() as $value)
			$elems[] = $value;
		
		return new Collection($elems ?? []);
	}
	
	/**
	 * Creates a generator that iterates over collection.
	 * @param bool $listall Should listable objects be yield as well?
	 * @yield mixed Collection's stored values.
	 * @return \Generator
	 */
	public function iterate(bool $listall = false): \Generator
	{
		foreach(parent::iterate() as $key => $value) {
			$check = listable($value);
			
			if(!$check || $check && $listall)
				yield $key => $value;
			
			if($check)
				yield from ($value instanceof Collection)
					? $value->iterate()
					: $value;
		}
	}
	
	/**
	 * Creates a new collection, the same type as the original, by using a
	 * function for creating the new elements based on the older ones. The
	 * callback receives the following parameters respectively: value, key.
	 * @param callable $fn Function to use for creating new elements.
	 * @return static New collection instance.
	 */
	public function map(callable $fn)
	{
		foreach(parent::iterate() as $key => $value)
			$result[$key] = listable($value)
				? $this->new($value)->map($fn)
				: $fn($value, $key);
		
		return $this->new($result ?? []);
	}
	
	/**
	 * Reduces collection to a single value calculated by callback.
	 * @param callable $fn Reducing function. Parameters: carry, value.
	 * @param mixed $initial Initial reduction value.
	 * @return mixed Resulting value from reduction.
	 */
	public function reduce(callable $fn, $initial = null)
	{
		return array_reduce($this->flatten()->raw(), $fn, $initial);
	}
	
	/**
	 * Iterates over collection and executes a function over every element.
	 * @param callable $fn Iteration function. Parameters: value, key.
	 * @param mixed|mixed[] $userdata Optional extra parameters for function.
	 * @return static Collection for method chaining.
	 */
	public function walk(callable $fn, $userdata = null)
	{
		$userdata = toArray($userdata);
		
		foreach(parent::iterate() as $key => $value)
			isListable($value)
				? $this->ref($value)->walk($fn, ...$userdata)
				: $fn($value, $key, ...$userdata);
		
		return $this;
	}
	
}
