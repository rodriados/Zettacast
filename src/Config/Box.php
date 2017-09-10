<?php
/**
 * Zettacast\Config\Box class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Config;

use Zettacast\Collection\Dot;
use ArrayAccess as ArrayAccessContract;
use Zettacast\Collection\Concerns\ArrayAccess;

class Box
	implements ArrayAccessContract
{
	use ArrayAccess;
	
	/**
	 * All data contained by the box.
	 * @var Dot Stores all data sent as configuration.
	 */
	protected $data;
	
	/**
	 * Box constructor.
	 * This constructor receives the initial values contained in the box.
	 * @param array $data The data to be stored in instance.
	 */
	public function __construct($data = [])
	{
		$this->data = new Dot($data);
	}
	
	/**
	 * Gets an element stored in collection.
	 * @param mixed $key Key of requested element.
	 * @param mixed $default Default value as fallback.
	 * @return mixed Requested element or default fallback.
	 */
	public function get($key, $default = null)
	{
		return $this->data->get($key, $default);
	}
	
	/**
	 * Checks whether element key exists.
	 * @param mixed $key Key to be check if exists.
	 * @return bool Does key exist?
	 */
	public function has($key) : bool
	{
		return $this->data->has($key);
	}
	
	/**
	 * Removes an element from collection.
	 * @param mixed $key Key to be removed.
	 * @return static Box for method chaining.
	 */
	public function remove($key)
	{
		$this->data->remove($key);
		return $this;
	}
	
	/**
	 * Sets a value to the given key.
	 * @param mixed $key Key to created or updated.
	 * @param mixed $value Value to be stored in key.
	 * @return static Box for method chaining.
	 */
	public function set($key, $value)
	{
		$this->data->set($key, $value);
		return $this;
	}
	
}
