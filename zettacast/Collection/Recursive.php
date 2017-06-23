<?php
/**
 * Zettacast\Collection\Recursive class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

/**
 * Recursive collection class. This collection implements recursive access
 * methods, that is it transforms its data into recursive collections.
 * @package Zettacast\Collection
 * @version 1.0
 */
class Recursive extends Basic
{
	/**
	 * Collapses the collection into a one level shallower collection.
	 * @return static The collapsed collection.
	 */
	public function collapse()
	{
		return $this->factory(
			array_reduce($this->data, function ($carry, $value) {
				return array_merge($carry, self::toarray($value));
			}, [])
		);
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
		$fn = $fn ?? function ($value) {
			return (bool)$value;
		};
		
		foreach($this->data as $key => $value)
			if($fn($value, $key) == !$invert)
				$result[$key] = self::traversable($value)
					? $this->factory($value)->filter($fn, $invert)->all()
					: $value;
		
		return $this->factory($result ?? []);
	}
	
	/**
	 * Flattens the recursive collection into a single level collection.
	 * @return Basic The flattened collection.
	 */
	public function flatten()
	{
		foreach($this->iterate() as $value)
			$list[] = $value;
		
		return new Basic($list ?? []);
	}
	
	/**
	 * Get an element stored in collection.
	 * @param mixed $key Key of requested element.
	 * @param mixed $default Default value fallback.
	 * @return mixed Requested element or default fallback.
	 */
	public function get($key, $default = null)
	{
		$value = $this->has($key)
			? $this->data[$key]
			: $default;
		
		return self::traversable($value)
			? $this->decorator($this->data[$key])
			: $value;
	}
		
	/**
	 * Checks whether an element exists in collection.
	 * @param mixed $needle Element being searched for.
	 * @param bool $strict Should types be strictly the same?
	 * @return bool Was the element found?
	 */
	public function in($needle, bool $strict = false)
	{
		return !$this->every(
			$strict
				? function ($val) use ($needle) { return $val === $needle; }
				: function ($val) use ($needle) { return $val ==  $needle; },
		    false
		);
	}
	
	/**
	 * Creates a generator that recursively iterates over the collection.
	 * @yield mixed Collection's recursively stored values.
	 */
	public function iterate()
	{
		foreach($this->data as $key => $value)
			self::traversable($value)
		        ? yield from $value
				: yield $key => $value;
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
		foreach($this->data as $key => $value)
			$result[$key] = self::traversable($value)
				? $this->factory($value)->map($fn)->all()
				: $fn($value, $key);
		
		return $this->factory($result ?? []);
	}
	
	/**
	 * Reduces collection to a single value calculated by callback.
	 * @param callable $fn Reducing function.
	 * @param mixed $initial Initial reduction value.
	 * @return mixed Resulting value from reduction.
	 */
	public function reduce(callable $fn, $initial = null)
	{
		foreach($this->iterate() as $value)
			$initial = $fn($initial, $value);
	
		return $initial;
	}
	
	/**
	 * Iterates over collection and executes a function over every element.
	 * @param callable $fn Iteration function. Parameters: value, key.
	 * @param mixed $userdata Optional third parameter for function.
	 * @return static Collection for method chaining.
	 */
	public function walk(callable $fn, $userdata = null)
	{
		foreach($this->data as $key => &$value)
			self::traversable($value)
				? $this->decorator($value)->walk($fn, $userdata)
				: $fn($value, $key, $userdata);
		
		return $this;
	}
	
}
