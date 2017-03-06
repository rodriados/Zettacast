<?php
/**
 * Helper\Contract\Facadable trait file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2016 Rodrigo Siqueira
 */
namespace Zettacast\Helper\Contract;

/**
 * Implements methods that allow class to become a façade. This trait turns
 * the object it is used within into an another object instance, so this
 * instance can be accessed by the usage of static methods.
 * @package Zettacast\Helper\Contract
 */
trait Facadable {
	
	/**
	 * Façaded object instance. This is the instance to be called when using a
	 * façaded method.
	 * @var mixed Façaded object instance.
	 */
	protected static $instance;
	
	/**
	 * Retrieves the instance of the object being façaded.
	 * @return mixed Façaded object instance.
	 */
	protected static function facaded() {
		
		$access = static::accessor();
		
		if(is_object($access))
			return $access;
		
		if(isset(self::$instance))
			return self::$instance;
		
		return self::$instance = zetta($access);
		
	}
	
	/**
	 * Handles dynamic static calls to the façaded object.
	 * @param string $method Method to be called.
	 * @param array $args Arguments for the called method.
	 * @return mixed Façaded method return value.
	 */
	public static function __callStatic(string $method, array $args) {
		
		$instance = static::facaded();
		return $instance->$method(...$args);
		
	}
	
	/**
	 * Informs what the façaded object accessor is, allowing it to be further
	 * used when calling façaded methods.
	 * @return mixed Façaded object accessor.
	 */
	protected abstract static function accessor();
	
}
