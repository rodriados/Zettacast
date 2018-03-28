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
	 * Depth-separator. This variable holds the symbol that indicates depth
	 * when iterating over the data. It defaults to a single dot.
	 * @var string Depth separator.
	 */
	protected $dot;
	
	/**
	 * Dot constructor.
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
			if(!listable($node) || !isset($node[$segment]))
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
			if(!listable($node) || !isset($node[$segment]))
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
			if(!isset($node[$segment]) || !listable($node[$segment]))
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
			if(!listable($node) or !isset($node[$segment]))
				return;
			
			$node instanceof Collection
				? ($node = &$node->data[$segment])
				: ($node = &$node[$segment]);
		}
		
		unset($node[$last]);
	}
	
	/**
	 * Filters elements according to given test. If no test function is given,
	 * it fallbacks to removing all false equivalent values.
	 * @param callable $fn Test function. Parameters: value, key.
	 * @return static Collection of all filtered values.
	 */
	public function filter(callable $fn = null)
	{
		static $scope = [];
		$fn = $fn ?? 'with';
		
		foreach($this->data as $key => $value) {
			array_push($scope, $key);
			
			if($fn($value, implode($this->dot, $scope)))
				$result[$key] = listable($value)
					? $this->new($value)->filter($fn)
					: $value;
			
			array_pop($scope);
		}
		
		return $this->new($result ?? []);
	}
	
	/**
	 * Plucks an array of values from collection.
	 * @param string|array $value Requested keys to pluck.
	 * @param string|array $key Keys to index plucked array.
	 * @return Collection The plucked values.
	 */
	public function pluck($value, $key = null): Collection
	{
		foreach($this->data as $item) {
			$ref = self::ref($item, $this);
			
			is_null($key) || !($keyvalue = $ref->get($key))
				? ($result[/*nokey*/] = $ref->get($value))
				: ($result[$keyvalue] = $ref->get($value));
		}
		
		return new Collection($result ?? []);
	}
	
	/**
	 * Creates a new instance of class based on an already existing instance.
	 * @param mixed $target Data to feed into the new instance.
	 * @return static The new instance.
	 */
	protected function new($target = [])
	{
		$obj = new static($target, $this->dot);
		return $obj;
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
