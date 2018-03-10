<?php
/**
 * Zettacast\Injector\InjectorException class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Injector;

use Zettacast\Error\Exception;

class InjectorException extends Exception
{
	/**
	 * Creates an Injector exception.
	 * @param string $abstract Non-existant object name.
	 * @return static Created exception.
	 */
	public static function doesntExist(string $abstract)
	{
		return new static(sprintf(
			_('The class "%s" does not exist.'),
			$abstract
		));
	}
	
	/**
	 * Creates an Injector exception.
	 * @param string $abstract Non-instantiable object name.
	 * @return static Created exception.
	 */
	public static function notInstantiable(string $abstract)
	{
		return new static(sprintf(
			_('The abstraction "%s" is not instantiable.'),
			$abstract
		));
	}
	
	/**
	 * Creates an Injector exception.
	 * @param string $abstract Object name which instantiation failed.
	 * @param string $param Parameter that cannot be resolved.
	 * @return static Created exception.
	 */
	public static function notResolvable(string $abstract, string $param)
	{
		return new static(sprintf(
			_('The parameter "$%s", needed by "%s", could not be resolved.'),
			$param, $abstract
		));
	}
	
	/**
	 * Creates an Injector exception.
	 * @param string $class Object name which instance is required.
	 * @param string $method Object scope method that requires an instance.
	 * @return static Created exception.
	 */
	public static function requiredInstance(string $class, string $method)
	{
		return new static(sprintf(
			_('An instance of "%s" is needed for wrapping "%s::%s".'),
			$class, $class, $method
		));
	}
}
