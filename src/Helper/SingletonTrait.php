<?php
/**
 * Zettacast\Helper\SingletonTrait trait file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Helper;

trait SingletonTrait
{
	/**
	 * Object's singleton instance. Every operation made into object in which
	 * this trait is used, this instance is called.
	 * @var static Object's singleton instance.
	 */
	private static $instance = null;
	
	/**
	 * Singleton instance discovery method. Gives access to the singleton or
	 * creates it if it has not yet been created.
	 * @return static Singleton instance.
	 */
	final public static function i(): self
	{
		if(!isset(self::$instance)) {
			self::$instance = new static;
			zetta()->set(static::class, self::$instance);
		}
		
		return self::$instance;
	}
	
	/**
	 * SingletonTrait constructor.
	 * The constructor is blocked. This does not allow Singleton objects to be
	 * instantiated out of context when a constructor is not needed.
	 */
	protected function __construct()
	{
		# Blocked constructor.
	}
	
	/**
	 * SingletonTrait clone magic method.
	 * Cloning is blocked. This does not allow Singleton objects to be cloned.
	 * Cloning is not possible when one wants keep one single instance.
	 */
	final protected function __clone()
	{
		# Blocked cloning.
	}
	
	/**
	 * SingletonTrait wakeup magic method.
	 * Unserialization is blocked. This does not allow Singleton objects to be
	 * instanciated via unserialization.
	 */
	final protected function __wakeup()
	{
		# Blocked unserialization.
	}
}
