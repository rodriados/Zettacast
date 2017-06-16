<?php
/**
 * Helper\Contract\Extendable trait file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2016 Rodrigo Siqueira
 */
namespace Zettacast\Helper;

use Closure;

/**
 * Implements methods that allow class extension. This trait allows the object
 * it is used within to be extended, that is to have methods included during
 * runtime. Added methods can be both for instances or static scopes.
 * @package Zettacast\Helper
 */
trait Extendable {
	
	/**
	 * The registered string functions.
	 * @var array Registered functions list.
	 */
	protected static $fn = [];
	
	/**
	 * Attaches a new method to the class by the creation of a new function.
	 * @param string $name Name of the method being attached.
	 * @param Closure $fn Code block for function being attached.
	 */
	public static function attach($name, Closure $fn) {
		
		self::$fn[$name] = $fn;
		
	}
	
	/**
	 * Extends the class allowing many methods to be attached it.
	 * @param array $functions Methods to be attached.
	 */
	public static function extend(array $functions) {
		
		foreach($functions as $name => $fn)
			static::attach($name, $fn);
		
	}
	
	/**
	 * Checks whether a function is registered.
	 * @param string $name function to be checked.
	 * @return bool Was function located?
	 */
	public static function attached($name) {
		
		return isset(self::$fn[$name]);
		
	}
	
	/**
	 * Tries to call a registered function within static scope.
	 * @param string $name Name of function being called.
	 * @param array $params Parameters passed to function.
	 * @return mixed Called function returned value.
	 */
	public static function __callStatic($name, $params) {
		
		if(!isset(self::$fn[$name]))
			throw new \BadMethodCallException("Method {$name} does not exist");
		
		$fn = Closure::bind(self::$fn[$name], null, static::class);
		return $fn(...$params);
		
	}
	
	/**
	 * Tries to call a registered function within instance scope.
	 * @param string $name Name of function being called.
	 * @param array $params Parameters passed to function.
	 * @return mixed Called function return value.
	 */
	public function __call($name, $params) {
		
		if(!isset(self::$fn[$name]))
			throw new \BadMethodCallException("Method {$name} does not exist");
		
		$fn = Closure::bind(self::$fn[$name], $this, static::class);
		return $fn(...$params);
			
	}
	
}
