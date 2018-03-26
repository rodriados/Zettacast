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
	 * @return static The created injector exception.
	 */
	public static function inexistant(string $abstract)
	{
		return static::format(
			_('The class "%1$s" does not exist.'),
			$abstract
		);
	}
	
	/**
	 * Creates an injector exception.
	 * @param string $abstract Non-instantiable object name.
	 * @return static The created injector exception.
	 */
	public static function uninstantiable(string $abstract)
	{
		return static::format(
			_('The abstraction "%1$s" is not instantiable.'),
			$abstract
		);
	}
	
	/**
	 * Creates an injector exception.
	 * @param string $abstract Object name which instantiation failed.
	 * @param string $param Parameter that cannot be resolved.
	 * @return static The created injector exception.
	 */
	public static function unresolvable(string $abstract, string $param)
	{
		return static::format(
			_('The parameter "%1$s" needed by "%2$s", could not be resolved.'),
			$param, $abstract
		);
	}
	
	/**
	 * Creates an injector exception.
	 * @param string $class Object name which instance is required.
	 * @param string $method Object scope method that requires an instance.
	 * @return static Created exception.
	 */
	public static function missing(string $class, string $method)
	{
		return static::format(
			_('An instance of "%1$s" is needed for wrapping "%1$s::%2$s".'),
			$class, $method
		);
	}
}
