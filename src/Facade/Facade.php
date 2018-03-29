<?php
/**
 * Zettacast\Facade\Facade class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Facade;

abstract class Facade
{
	/**
	 * Keeps all façade instances in a single place. This allows us to use many
	 * different façades at the same time, using their accessors as an index.
	 * @var array Façaded objects instances.
	 */
	private static $instance = [];
	
	/**
	 * Handles dynamic static calls to façaded object.
	 * @param string $method Method called.
	 * @param array $args Arguments for the called method.
	 * @return mixed Façaded method's return value.
	 */
	public static function __callStatic(string $method, array $args)
	{
		$instance = static::i();
		return $instance->$method(...$args);
	}
	
	/**
	 * Retrieves instance of object being façaded.
	 * @return object Façaded object instance.
	 */
	protected static function i(): object
	{
		if(is_object($accessor = static::accessor()))
			return $accessor;
		
		if(isset(self::$instance[$accessor]))
			return self::$instance[$accessor];
		
		return self::$instance[$accessor] = zetta($accessor);
	}
	
	/**
	 * Informs what object is being façaded, thus allowing it to be further
	 * used when calling façaded methods.
	 * @return mixed Façaded object accessor.
	 */
	protected abstract static function accessor();
}
