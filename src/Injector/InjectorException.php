<?php
/**
 * Zettacast\Injector\InjectorException class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Injector;

use Zettacast\Exception\Exception;

/**
 * This class informs an error occurred during execution of module injector.
 * @package Zettacast\Injector
 * @version 1.0
 */
class InjectorException extends Exception
{
	/**
	 * Creates an injector exception.
	 * @param string $abstract Non-existant object name.
	 * @return static Created exception.
	 */
	public static function inexistant(string $abstract)
	{
		return new static(sprintf(
			_('The class "%s" does not exist.'),
			$abstract
		));
	}
	
	/**
	 * Creates an injector exception.
	 * @param string $abstract Non-instantiable object name.
	 * @return static Created exception.
	 */
	public static function uninstantiable(string $abstract)
	{
		return new static(sprintf(
			_('The abstraction "%s" is not instantiable.'),
			$abstract
		));
	}
	
	/**
	 * Creates an injector exception.
	 * @param string $abstract Object name which instantiation failed.
	 * @param string $param Parameter that cannot be resolved.
	 * @return static Created exception.
	 */
	public static function unresolvable(string $abstract, string $param)
	{
		return new static(sprintf(
			_('The parameter "$%s", needed by "%s", could not be resolved.'),
			$param, $abstract
		));
	}
	
	/**
	 * Creates an injector exception.
	 * @param string $class Object name which instance is required.
	 * @param string $method Object scope method that requires an instance.
	 * @return static Created exception.
	 */
	public static function missing(string $class, string $method)
	{
		return new static(sprintf(
			_('An instance of "%s" is needed for wrapping "%s::%s".'),
			$class, $class, $method
		));
	}
}
