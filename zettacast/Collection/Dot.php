<?php
/**
 * Zettacast\Collection\Dot class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

use Zettacast\Collection\Contract\Collection;

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
	 * @var string Collection's scope.
	 */
	protected $scope = null;
	
	/**
	 * Dot constructor. This constructor simply sets the data received as the
	 * data stored in collection.
	 * @param array|Collection|\Traversable $data Data to be stored.
	 * @param string $dot Depth-separator.
	 */
	public function __construct($data = null, string $dot = '.')
	{
		$this->dot = $dot;
		parent::__construct($data);
	}
	
	/**
	 * Removes an element from collection.
	 * @param mixed $key Key to be removed.
	 */
	public function del($key)
	{
		$segment = $this->dot($key);
		$lastkey = array_pop($segment);
		$curnode = &$this->data;
		
		foreach($segment as $dot) {
			if(!self::listable($curnode) or !isset($curnode[$dot]))
				return;
			
			$curnode = &$curnode[$dot];
		}
		
		unset($curnode[$lastkey]);
	}
	
	/**
	 * Explodes dot expression into array.
	 * @param mixed $key Dot expression key to be split.
	 * @return array Dot expression segments.
	 */
	protected function dot($key)
	{
		return explode($this->dot, $key);
	}
	
	/**
	 * Filters elements according to the given test. If no test function is
	 * given, it fallbacks to removing all false equivalent values.
	 * @param callable $fn Test function. Parameters: value, key.
	 * @return static Collection of all filtered values.
	 */
	public function filter(callable $fn)
	{
		$scope = !is_null($this->scope) ? $this->scope.$this->dot : null;
		
		foreach(Base::iterate() as $key => $value)
			if($fn($value, $scope . $key))
				$result[$key] = self::listable($value)
					? $this->scrf($value, $scope.$key)->filter($fn)->all()
					: $value;
		
		return new static($result ?? []);
	}
		
	/**
	 * Gets an element stored in collection.
	 * @param mixed $key Key of requested element.
	 * @param mixed $default Default value fallback.
	 * @param bool $ref Should Collection be returned if element is array?
	 * @return mixed Requested element or default fallback.
	 */
	public function get($key, $default = null, $ref = true)
	{
		$segment = $this->dot($key);
		$curnode = &$this->data;
		
		foreach($segment as $dot) {
			if(!self::listable($curnode) or !isset($curnode[$dot]))
				return $default;
			
			$curnode = &$curnode[$dot];
		}
		
		return $ref ? self::ref($curnode) : $curnode;
	}
	
	/**
	 * Checks whether element key exists.
	 * @param mixed $key Key to be check if exists.
	 * @return bool Does key exist?
	 */
	public function has($key)
	{
		$segment = $this->dot($key);
		$curnode = &$this->data;
		
		foreach($segment as $dot) {
			if(!self::listable($curnode) or !isset($curnode[$dot]))
				return false;
			
			$curnode = &$curnode[$dot];
		}
		
		return true;
	}
	
	/**
	 * Creates a new collection with a subset of elements.
	 * @param mixed|array $keys Keys to be included in new collection.
	 * @return static New collection instance.
	 */
	public function only($keys)
	{
		$keys = self::toarray($keys);
		$result = new static;
		
		foreach($keys as $key)
			if($this->has($key))
				$result->set($key, $this->get($key, null, false));
		
		return $result;
	}
	
	/**
	 * Plucks an array of values from collection.
	 * @param string|array $value Requested keys to pluck.
	 * @param string|array $key Keys to index plucked array.
	 * @return Basic The plucked values.
	 */
	public function pluck($value, $key = null)
	{
		foreach(Base::iterate() as $item) {
			$i = $this->scrf($item);
			
			if(is_null($key)) $result[] = $i->get($value, null, false);
			else $result[$i->get($key)] = $i->get($value, null, false);
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
		$segment = $this->dot($key);
		$lastkey = array_pop($segment);
		$curnode = &$this->data;

		foreach($segment as $dot) {
			if(!isset($curnode[$dot]) or !self::listable($curnode[$dot]))
				$curnode[$dot] = [];
			
			$curnode = &$curnode[$dot];
		}
		
		$curnode[$lastkey] = $value;
	}
	
	/**
	 * Creates a new collection mantaining the reference to the original
	 * variable that is the data stored in it and sets a scope.
	 * @param mixed $data Data to be stored in collection.
	 * @param string $scope Scope to be attached to collection.
	 * @return static New collection with referenced data.
	 */
	protected function scrf(&$data, $scope = null)
	{
		if(!is_array($data) and !$data instanceof Collection)
			return $data;
		
		$refobj = new static(null, $this->dot);
		$refobj->data = &$data;
		$refobj->scope = $scope;
		
		return $refobj;
	}
	
}
