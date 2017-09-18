<?php
/**
 * Zettacast\Contract\SingletonContract trait file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2016 Rodrigo Siqueira
 */
namespace Zettacast\Contract;

/**
 * Implements singleton methods. This trait transforms every class in which it
 * is used in a singleton object. Singletons are instantiated only once and
 * everytime a method within a singleton object is called, the unique instance
 * of the object is used.
 * @package Zettacast\Contract
 * @version 1.0
 */
trait SingletonTrait
{
	/**
	 * Object's singleton instance. Every operation made into the object in
	 * which this trait is used, this instance is called.
	 * @var static Object's singleton instance.
	 */
	private static $instance = null;
	
	/**
	 * Singleton instance discovery. This method gives access to the singleton
	 * or creates it if it's not yet created.
	 * @return static Singleton instance.
	 */
	final public static function instance()
	{
		if(!isset(self::$instance)) {
			self::$instance = new static;
			zetta()->share(static::class, self::$instance);
		}
		
		return self::$instance;
	}
	
	/**
	 * Blocks constructor. This does not allow Singleton objects to be
	 * instantiated out of context when a constructor is not needed.
	 */
	protected function __construct()
	{
		;
	}
	
	/**
	 * Blocks clone magic method. This does not allow Singleton objects to be
	 * cloned. Cloning is not possible when one wants keep one single instance.
	 */
	final protected function __clone()
	{
		;
	}
	
	/**
	 * Blocks wakeup magic method. This does not allow Singleton objects to be
	 * instanciated via unserialization.
	 */
	final protected function __wakeup()
	{
		;
	}
	
}
