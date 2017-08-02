<?php
/**
 * Zettcast\Helper\Fluent class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2016 Rodrigo Siqueira
 */
namespace Zettacast\Helper;

use Closure;

/**
 * Allows the creation of Fluent classes, that is it can have methods and
 * properties attached to its instances independently after instantiation.
 * @package Zettacast\Helper
 */
class Fluent
{
	/**
	 * Registered methods and properties.
	 * @var array All fluent data available for this object.
	 */
	private $fluent = [];
	
	/**
	 * Retrieves a property fluently stored in the object.
	 * @param string $name Name of requested property.
	 * @return mixed The value stored in the property or null otherwise.
	 */
	public function __get(string $name)
	{
		return !($this->fluent[$name] ?? null) instanceof Closure
			? $this->fluent[$name]
			: null;
	}
	
	/**
	 * Fluently stores a new property or method into the object.
	 * @param string $name Name to be stored.
	 * @param mixed $data Property value or method body to be attached.
	 */
	public function __set(string $name, $data)
	{
		$this->fluent[$name] = $data instanceof Closure
			? Closure::bind($data, $this, static::class)
			: $data;
	}
	
	/**
	 * Fluently unsets a property or method registered in the object.
	 * @param string $name Name to be unset.
	 */
	public function __unset(string $name)
	{
		unset($this->fluent[$name]);
	}
	
	/**
	 * Calls a method fluently attached to object.
	 * @param string $fn Method name to be called.
	 * @param array $args Arguments to be passed to method.
	 * @return mixed Method return value or null otherwise.
	 */
	public function __call(string $fn, array $args)
	{
		return ($this->fluent[$fn] ?? null) instanceof Closure
			? $this->fluent[$fn](...$args)
			: null;
	}
	
}
