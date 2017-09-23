<?php
/**
 * Zettacast\Exception\Injector\InjectorException exception file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Exception\Injector;

use Zettacast\Exception\Exception;

class InjectorException extends Exception
{
	public static function doesNotExist(string $abstract)
	{
		return new static(sprintf(
			'The class "%s" does not exist.',
			$abstract
		));
	}
	
	public static function notInstantiable(string $abstract)
	{
		return new static(sprintf(
			'The abstraction "%s" is not instantiable.',
			$abstract
		));
	}
	
	public static function notResolvable(string $abstract, string $param)
	{
		return new static(sprintf(
			'The parameter "$%s", needed by "%s", could not be resolved.',
			$param, $abstract
		));
	}
	
	public static function requiredInstance(string $class, string $method)
	{
		return new static(sprintf(
			'An instance of "%s" is needed for wrapping "%s::%s".',
			$class, $class, $method
		));
	}
	
}
