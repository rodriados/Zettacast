<?php
/**
 * Zettacast\Helper\FacadeAbstract class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Helper;

abstract class FacadeAbstract
{
	/**
	 * Façaded object instance. This is the instance called when calling for a
	 * method of a façaded class.
	 * @var mixed Façaded object instance.
	 */
	private static $instance = null;
	
	/**
	 * Handle dynamic static calls to façaded object.
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
	 * Retrieve instance of object being façaded.
	 * @return object Façaded object instance.
	 */
	protected static function i(): object
	{
		if(isset(self::$instance))
			return self::$instance;
		
		$access = static::accessor();
		
		return self::$instance = !is_object($access)
			? zetta($access)
			: $access;
	}
	
	/**
	 * Inform what object is being façaded, thus allowing it to be further used
	 * when calling façaded methods.
	 * @return mixed Façaded object accessor.
	 */
	protected abstract static function accessor();
}
