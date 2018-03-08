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
	 * Singleton instance discovery. Give access to the singleton or create it
	 * if it's not yet created.
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
	 * Block constructor. This does not allow Singleton objects to be
	 * instantiated out of context when a constructor is not needed.
	 */
	protected function __construct()
	{
	}
	
	/**
	 * Block clone magic method. This does not allow Singleton objects to be
	 * cloned. Cloning is not possible when one wants keep one single instance.
	 */
	final protected function __clone()
	{
	}
	
	/**
	 * Block wakeup magic method. This does not allow Singleton objects to be
	 * instanciated via unserialization.
	 */
	final protected function __wakeup()
	{
	}
}
