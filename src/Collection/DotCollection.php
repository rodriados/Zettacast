<?php
/**
 * Zettacast\Collection\DotCollection class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

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
	 * This constructor simply sets the data received as the data stored in
	 * collection, and defines the depth-separator.
	 * @param array|\Traversable $data Data to store.
	 * @param string $dot Depth-separator.
	 */
	public function __construct($data = null, string $dot = '.')
	{
		$this->dot = $dot;
		parent::__construct($data);
	}
	
	/**
	 * @inheritdoc
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
		
		return $this->ref($node);
	}
	
	/**
	 * @inheritdoc
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
	 * @inheritdoc
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
	 * @inheritdoc
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
	 * @inheritdoc
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
	 * Pluck an array of values from collection.
	 * @param string|array $value Requested keys to pluck.
	 * @param string|array $key Keys to index plucked array.
	 * @return Collection The plucked values.
	 */
	public function pluck($value, $key = null): Collection
	{
		foreach($this->data as $item) {
			$ref = $this->ref($item);
			
			is_null($key) || !($keyvalue = $ref->get($key))
				? ($result[/*nokey*/] = $ref->get($value))
				: ($result[$keyvalue] = $ref->get($value));
			
		}
		
		return new Collection($result ?? []);
	}
	
	/**
	 * @inheritdoc
	 */
	protected function new($target = [])
	{
		$obj = new static($target, $this->dot);
		return $obj;
	}
	
	/**
	 * Explode dot expression into array.
	 * @param string $key Dot expression key to split.
	 * @return array Dot expression segments.
	 */
	protected function undot(string $key): array
	{
		return explode($this->dot, trim($key, $this->dot));
	}
}
