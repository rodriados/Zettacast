<?php
/**
 * Zettacast\Collection\DotCollection class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

/**
 * The dot collection class. This collection implements dot access methods,
 * that is it's possible to access its recursive data via dot notation.
 * @package Zettacast\Collection
 * @version 1.0
 */
class DotCollection extends RecursiveCollection
{
	/**
	 * Depth-separator. This property holds the symbol that indicates depth
	 * when iterating over the data. It defaults to a single dot.
	 * @var string Depth separator.
	 */
	protected $dot;
	
	/**
	 * Key prefix. This property holds the prefix of this instance's keys. This
	 * is only used when performing recursive actions.
	 * @var string Key prefix.
	 */
	protected $prefix = null;
	
	/**
	 * DotCollection constructor.
	 * Sets given data as the data stored by collection and defines it's depth
	 * separator indicator, which is usually a dot.
	 * @param mixed $data Data to store.
	 * @param string $dot Depth-separator.
	 */
	public function __construct($data = null, string $dot = '.')
	{
		$this->dot = $dot;
		parent::__construct($data);
	}
	
	/**
	 * Gets an element stored in collection.
	 * @param mixed $key Key of requested element.
	 * @param mixed $default Default value fallback.
	 * @return mixed Requested element or default fallback.
	 */
	public function get($key, $default = null)
	{
		$dot = $this->undot($key);
		$node = &$this->data;
		
		foreach($dot as $segment) {
			if(!iterable($node) || !isset($node[$segment]))
				return $default;
			
			$node instanceof Collection
				? ($node = &$node->data[$segment])
				: ($node = &$node[$segment]);
		}
		
		return self::ref($node, $this);
	}
	
	/**
	 * Checks whether element key exists.
	 * @param mixed $key Key to check existance.
	 * @return bool Does key exist?
	 */
	public function has($key): bool
	{
		$dot = $this->undot($key);
		$node = &$this->data;
		
		foreach($dot as $segment) {
			if(!iterable($node) || !isset($node[$segment]))
				return false;
			
			$node instanceof Collection
				? ($node = &$node->data[$segment])
				: ($node = &$node[$segment]);
		}
		
		return true;
	}
	
	/**
	 * Sets a value to given key.
	 * @param mixed $key Key to create or update.
	 * @param mixed $value Value to store in key.
	 */
	public function set($key, $value): void
	{
		$dot = $this->undot($key);
		$last = array_pop($dot);
		$node = &$this->data;
		
		foreach($dot as $segment) {
			if(!isset($node[$segment]) || !iterable($node[$segment]))
				$node[$segment] = [];
			
			$node instanceof Collection
				? ($node = &$node->data[$segment])
				: ($node = &$node[$segment]);
		}
		
		$node[$last] = $value;
	}
	
	/**
	 * Removes an element from collection.
	 * @param mixed $key Key to remove.
	 */
	public function del($key): void
	{
		$dot = $this->undot($key);
		$last = array_pop($dot);
		$node = &$this->data;
		
		foreach($dot as $segment) {
			if(!iterable($node) or !isset($node[$segment]))
				return;
			
			$node instanceof Collection
				? ($node = &$node->data[$segment])
				: ($node = &$node[$segment]);
		}
		
		unset($node[$last]);
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
			$this->data[$key] = iterable($value)
				? $this->new($value, $key)->apply($fn, ...$userdata)
				: $fn($value, $this->prefix($key), ...$userdata);
		
		return $this;
	}
	
	/**
	 * Filters elements according to given test. If no test function is given,
	 * it fallbacks to removing all false equivalent values.
	 * @param callable $fn Test function. Parameters: value, key.
	 * @return static Collection of all filtered values.
	 */
	public function filter(callable $fn = null)
	{
		$fn = $fn ?? 'with';
		
		foreach($this->iterate() as $key => $value)
			if($fn($value, $this->prefix($key)))
				$result[$key] = iterable($value)
					? $this->new($value, $key)->filter($fn)
					: $value;
		
		return $this->new($result ?? []);
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
		foreach($this->iterate() as $key => $value)
			$result[$key] = iterable($value)
				? $this->new($value, $key)->map($fn)
				: $fn($value, $this->prefix($key));
		
		return $this->new($result ?? []);
	}
	
	/**
	 * Plucks an array of values from collection.
	 * @param string|array $value Requested keys to pluck.
	 * @param string|array $key Keys to index plucked array.
	 * @return static The plucked values.
	 */
	public function pluck($value, $key = null)
	{
		foreach($this->iterate() as $item) {
			$ref = self::ref($item, $this);
			
			is_null($key) || !($keyvalue = $ref->get($key))
				? ($result[/*nokey*/] = $ref->get($value))
				: ($result[$keyvalue] = $ref->get($value));
		}
		
		return $this->new($result ?? []);
	}
	
	/**
	 * Iterates over collection and executes a function over every element.
	 * @param callable $fn Iteration function. Parameters: value, key.
	 * @param mixed $userdata Optional extra parameters for function.
	 * @return static Collection for method chaining.
	 */
	public function walk(callable $fn, $userdata = null)
	{
		$userdata = toarray($userdata);
		
		foreach($this->iterate() as $key => $value)
			iterable($value)
				? $this->new($value, $key)->walk($fn, ...$userdata)
				: $fn($value, $this->prefix($key), ...$userdata);
		
		return $this;
	}
	
	/**
	 * Creates a new instance of class based on an already existing instance.
	 * @param mixed $target Data to feed into the new instance.
	 * @param string $prefix New prefix to append to new instance.
	 * @return static The new instance.
	 */
	protected function new($target = [], string $prefix = null)
	{
		$obj = new static($target, $this->dot);
		$obj->prefix = $this->prefix && $prefix
			? $this->prefix.$this->dot.$prefix
			: ($prefix ?: $this->prefix);
		
		return $obj;
	}
	
	/**
	 * Applies the current prefix, if any, to the given key.
	 * @param string $key Key to prefix.
	 * @return string The prefixed key.
	 */
	protected function prefix(string $key): string
	{
		return $this->prefix
			? $this->prefix.$this->dot.$key
			: $key;
	}
	
	/**
	 * Explodes dot expression into array.
	 * @param string $key Dot expression key to split.
	 * @return array Dot expression segments.
	 */
	protected function undot(string $key): array
	{
		return explode($this->dot, trim($key, $this->dot));
	}
}
