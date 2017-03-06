<?php
/**
 * Collection façade file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Facade;

use Zettacast\Collection\Dot;
use Zettacast\Collection\Imutable;
use Zettacast\Collection\Recursive;
use Zettacast\Collection\Basic as baseclass;
use Zettacast\Helper\Contract\Extendable;

/**
 * The Collection façade is a factory for all other collection types. This
 * class can be instanciated as a default collection but can also be used to
 * instanciate other collection types.
 * @version 1.0
 */
final class Collection {
	
	use Extendable { Extendable::__callStatic as __callAttached; }
	
	/**
	 * Builds a new collection based on a function.
	 * @param array $data Initial data to be fed to generating function.
	 * @param callable $fn Function to build collection. Params: key, value.
	 * @return baseclass New collection instance.
	 */
	public static function build($data, callable $fn) {
		$collection = self::make();

		foreach($data as $key => $value) {
			list($_key, $_value) = $fn($key, $value);
			$collection[$_key] = $_value;
		}
		
		return $collection;
		
	}
	
	/**
	 * Creates a collection using an array for keys and another one for values.
	 * @param array $keys Array to be used as keys.
	 * @param array $values Array to be used as values.
	 * @return baseclass New collection instance.
	 */
	public static function combine($keys, $values) {
		
		return self::make(array_combine($keys, $values));
		
	}
	
	/**
	 * Recursive dot collection factory method.
	 * @param array $data Data to be set in collection.
	 * @param string $dot Depth-separator.
	 * @return Dot New collection instance.
	 */
	public static function dot($data = [], string $dot = '.') {
		
		return new Dot($data, $dot);
		
	}
	
	/**
	 * Creates a new collection and fills it with the given value.
	 * @param mixed $value Value to fill collection with.
	 * @param array|int $key Array of keys to be created or initial int key.
	 * @param int $count Number of elements to be filled if keys are integers.
	 * @return baseclass New collection instance.
	 */
	public static function fill($value, $key, int $count = 0) {
		
		return self::make(is_array($key)
			? array_fill_keys($key, $value)
			: array_fill($key, $count, $value));
		
	}
	
	/**
	 * Imutable collection factory method.
	 * @param array $data Data to be set in collection.
	 * @return Imutable New collection instance.
	 */
	public static function lock($data = []) {
		
		return new Imutable($data);
		
	}
	
	/**
	 * Simple collection factory method.
	 * @param array $data Data to be set in collection.
	 * @return baseclass New collection instance.
	 */
	public static function make($data = []) {
		
		return new baseclass($data);
		
	}
	
	/**
	 * Recursive collection factory method.
	 * @param array $data Data to be set in collection.
	 * @return Recursive New collection instance.
	 */
	public static function recursive($data = []) {
		
		return new Recursive($data);
		
	}
	
	/**
	 * Executes collection functions in built-in arrays.
	 * @param string $method Method to be called.
	 * @param array $args Arguments for the called method.
	 * @return mixed Façaded method return value.
	 */
	public static function __callStatic(string $method, array $args) {
		
		if(count($args) >= 1 and is_callable([baseclass::class, $method]))
			return self::make(array_shift($args))->$method(...$args);
		
		return self::__callAttached($method, $args);
		
	}
		
}
