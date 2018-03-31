<?php
/**
 * Zettacast\Support\MultitonTrait trait file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Support;

trait MultitonTrait
{
	/**
	 * Object's multiton instances. Every operation made into object in which
	 * this trait is used, one of these instance is called.
	 * @var static[] Object's multiton instances.
	 */
	private static $instances = [];
	
	/**
	 * Multiton instance discovery method. Gives access to an instance or
	 * creates it if it has not yet been created.
	 * @param string $name The name of instance to recover.
	 * @return static The recovered multiton instance.
	 */
	final public static function i(string $name): self
	{
		if(!isset(self::$instances[$name])) {
			self::$instances[$name] = new static;
			zetta()->set(static::class.':'.$name, self::$instances[$name]);
		}
		
		return self::$instances[$name];
	}
	
	/**
	 * MultitonTrait constructor.
	 * The constructor is blocked. This does not allow Singleton objects to be
	 * instantiated out of context when a constructor is not needed.
	 */
	protected function __construct()
	{
		# Blocked constructor.
	}
	
	/**
	 * MultitonTrait clone magic method.
	 * Cloning is blocked. This does not allow Singleton objects to be cloned.
	 * Cloning is not possible when one wants keep one single instance.
	 */
	final protected function __clone()
	{
		# Blocked cloning.
	}
	
	/**
	 * MultitonTrait wakeup magic method.
	 * Unserialization is blocked. This does not allow Singleton objects to be
	 * instanciated via unserialization.
	 */
	final protected function __wakeup()
	{
		# Blocked unserialization.
	}
}
