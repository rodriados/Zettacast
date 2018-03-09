<?php
/**
 * Zettacast\Collection\RecursiveCollection class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

class RecursiveCollection extends Collection
{
	/**
	 * @inheritdoc
	 */
	public function get($key, $default = null)
	{
		return $this->has($key)
			? $this->ref($this->data[$key])
			: $default;
	}
	
	/**
	 * @inheritdoc
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
	 * Collapse collection into a one level shallower collection.
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
	 * @inheritdoc
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
	 * Flatten collection into a single level collection.
	 * @return Collection The flattened collection.
	 */
	public function flatten(): Collection
	{
		foreach($this->iterate() as $value)
			$elems[] = $value;
		
		return new Collection($elems ?? []);
	}
	
	/**
	 * Create a generator that iterates over collection.
	 * @param bool $listall Should listable objects be yield as well?
	 * @yield mixed Collection's stored values.
	 * @return \Generator The generator created.
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
	 * @inheritdoc
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
	 * @inheritdoc
	 */
	public function reduce(callable $fn, $initial = null)
	{
		return array_reduce($this->flatten()->raw(), $fn, $initial);
	}
	
	/**
	 * @inheritdoc
	 */
	public function walk(callable $fn, $userdata = null)
	{
		foreach(parent::iterate() as $key => $value)
			listable($value)
				? $this->ref($value)->walk($fn, ...toarray($userdata))
				: $fn($value, $key, ...toarray($userdata));
		
		return $this;
	}
}
