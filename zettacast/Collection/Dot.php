<?php
/**
 * Zettacast\Collection\Dot class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

use Zettacast\Collection\Basic as Collection;

/**
 * Dot collection class. This collection implements dot access methods, that is
 * it's possible to access its recursive data via a dot string.
 * @package Zettacast\Collection
 * @version 1.0
 */
class Dot extends Recursive
{
	/**
	 * Depth-separator. This variable holds the symbol that indicates depth
	 * when iterating over the data. It defaults to a single dot.
	 * @var string Depth separator.
	 */
	protected $dot;
	
	/**
	 * Indicates scope the collection is inserted into. It defaults to nothing.
	 * @var array Collection's scope.
	 */
	protected $scope = null;
	
	/**
	 * Dot constructor. This constructor simply sets the data received as the
	 * data stored in collection.
	 * @param array|Collection|\Traversable $data Data to be stored.
	 * @param string $dot Depth-separator.
	 * @param string $scope Scope to which this Collection is attached to.
	 */
	public function __construct(
		$data = null,
		string $dot = '.',
		string $scope = null
	) {
		$this->dot = $dot;
		$this->scope = !is_null($scope) ? explode($dot, $scope) : [];
		
		parent::__construct($data);
	}
	
	/**
	 * Removes an element from collection.
	 * @param mixed $key Key to be removed.
	 */
	public function del($key)
	{
		$segments = $this->dot($key);
		$last = array_pop($segments);
		$node = &$this->data;
		
		foreach($segments as $segment) {
			if(!self::traversable($node) or !isset($node[$segment]))
				return;
			
			$node = &$this->inref($node, $segment);
		}
		
		unset($node[$last]);
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
			if($fn($value, $s = array_merge($this->scope, [$key])) == !$invert)
				$result[$key] = self::traversable($value)
					? $this->outref($value, $s)->filter($fn)->all()
					: $value;
		
		$result = $result ?? [];
		return $this->outref($result, $this->scope);
	}
		
	/**
	 * Gets an element stored in collection.
	 * @param mixed $key Key of requested element.
	 * @param mixed $default Default value fallback.
	 * @return mixed Requested element or default fallback.
	 */
	public function get($key, $default = null)
	{
		$segments = $this->dot($key);
		$scope = $this->scope;
		$node = &$this->data;
		
		foreach($segments as $segment) {
			if(!self::traversable($node) or !isset($node[$segment]))
				return $default;
			
			$node = &$this->inref($node, $segment);
			$scope[] = $segment;
		}
		
		return $this->outref($node, $scope);
	}
	
	/**
	 * Checks whether element key exists.
	 * @param mixed $key Key to be check if exists.
	 * @return bool Does key exist?
	 */
	public function has($key)
	{
		$segments = $this->dot($key);
		$node = $this->data;
		
		foreach($segments as $segment) {
			if(!self::traversable($node) or !isset($node[$segment]))
				return false;
			
			$node = $node[$segment];
		}
		
		return true;
	}
	
	/**
	 * Plucks an array of values from collection.
	 * @param string|array $value Requested keys to pluck.
	 * @param string|array $key Keys to index plucked array.
	 * @return Basic The plucked values.
	 */
	public function pluck($value, $key = null)
	{
		foreach($this->data as $item) {
			$i = $this->outref($item);
			
			if(is_null($key)) $result[] = $i->get($value);
			else $result[$i->get($key)] = $i->get($value);
		}
		
		return new Basic($result ?? []);
	}
	
	/**
	 * Sets a value to the given key.
	 * @param mixed $key Key to created or updated.
	 * @param mixed $value Value to be stored in key.
	 */
	public function set($key, $value)
	{
		$segments = $this->dot($key);
		$last = array_pop($segments);
		$node = &$this->data;
		
		foreach($segments as $segment) {
			if(!isset($node[$segment]) or !self::traversable($node[$segment]))
				$node[$segment] = [];
			
			$node = &$this->inref($node, $segment);
		}
		
		$node[$last] = $value;
	}
	
	/**
	 * Creates a new instance of class based on an already existing instance,
	 * and using by-reference assignment to data stored in new instance.
	 * @param mixed &$target Data to be fed into the new instance.
	 * @param array $scope Scope to be attached to collection.
	 * @return static The new instance.
	 */
	protected function decorator(&$target, array $scope = null)
	{
		$obj = new static(null, $this->dot);
		$obj->scope = $scope ?? $this->scope;
		$obj->data = &$target;
		return $obj;
	}
	
	/**
	 * Explodes dot expression into array.
	 * @param string $key Dot expression key to be split.
	 * @return array Dot expression segments.
	 */
	protected function dot(string $key)
	{
		$expanded = explode($this->dot, trim($key, $this->dot));
		$intersect = array_intersect($this->scope, $expanded);
		
		return  $this->scope === $intersect
			? array_slice($expanded, count($this->scope))
			: $expanded;
	}
	
	/**
	 * Creates a new instance of class based on an already existing instance.
	 * @param mixed $target Data to be fed into the new instance.
	 * @param array $scope Scope to be attached to collection.
	 * @return static The new instance.
	 */
	protected function factory($target = [], array $scope = null)
	{
		$obj = new static($target, $this->dot);
		$obj->scope = $scope ?? $this->scope;
		return $obj;
	}
	
	/**
	 * Gets an internal value by-reference if possible.
	 * @param array|Collection $node Instance from which value is retrieved.
	 * @param string $segment Segment to get reference from.
	 * @return mixed Retrieve value's reference.
	 */
	protected function &inref(&$node, string $segment)
	{
		if($node instanceof Collection)
			return $node->data[$segment];
		
		return $node[$segment];
	}
	
	/**
	 * Creates a new collection mantaining the reference to the original
	 * variable that is the data stored in it and sets a scope.
	 * @param mixed $data Data to be stored in collection.
	 * @param array $scope Scope to be attached to collection.
	 * @return static New collection with referenced data.
	 */
	protected function outref(&$data, array $scope = null)
	{
		if(!is_array($data) and !$data instanceof Collection)
			return $data;
		
		return $this->decorator($data, $scope);
	}
	
}
