<?php
/**
 * Zettacast\Config\Config class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Config;

use Zettacast\Collection\Dot;
use Zettacast\Collection\Concerns\ArrayAccess;
use Zettacast\Contract\Collection\Collection;

/**
 * This class holds configuration values to be sent to object constructors or
 * the like. It cannot access application's configuration files.
 * @version 1.0
 */
class Config
	implements Collection
{
	use ArrayAccess;
	
	/**
	 * Keeps track of the identifier for original instance.
	 * @var string Original instance object hash.
	 */
	private $hash;
	
	/**
	 * The data for all configuration instances is held in this property.
	 * @var Dot Stores all data sent as configuration.
	 */
	private static $data = null;
	
	/**
	 * Config constructor. The data sent through the constructor is permanent
	 * during all of instance existence.
	 * @param array $config The data to be stored in instance.
	 */
	public function __construct(array $config = [])
	{
		if(is_null(self::$data))
			self::$data = new Dot;
		
		$this->hash = spl_object_hash($this);
		self::$data->set($this->hash, ['count' => 1, 'data' => $config]);
	}
	
	/**
	 * Keeps track of the number of instance copies created, allowing the data
	 * to be kept until the last one is destroyed.
	 */
	public function __clone()
	{
		++self::$data[$this->hash]->count;
	}
	
	/**
	 * Destroys an instance and checks whether any other still exists. If no
	 * other instance is registered, the data is destroyed as well.
	 */
	public function __destruct()
	{
		if(--self::$data[$this->hash]->count < 1)
			self::$data->remove($this->hash);
	}
	
	/**
	 * Gets an element stored in collection.
	 * @param mixed $key Key of requested element.
	 * @param mixed $default Default value for requested element.
	 * @return mixed Requested element or default fallback.
	 */
	public function get($key, $default = null)
	{
		return self::$data[$this->hash]->data->get($key, $default);
	}
	
	/**
	 * Checks whether element key exists.
	 * @param mixed $key Key to be check if exists.
	 * @return bool Does key exist?
	 */
	public function has($key) : bool
	{
		return self::$data[$this->hash]->data->has($key);
	}
	
	/**
	 * This configuration collection does not allow changes to its contents.
	 * Thus, all data given to collection by creation time, will be kept until
	 * instances no longer exist.
	 * @param mixed $key Key to be removed, but will not be.
	 */
	public function remove($key)
	{
		;
	}
	
	/**
	 * This configuration collection does not allow changes to its contents.
	 * Thus, all data given to collection by creation time, will be kept until
	 * instances no longer exist.
	 * @param mixed $key Key to be set, but will not be.
	 * @param mixed $value Value to be put into collection, but will not be.
	 */
	public function set($key, $value)
	{
		;
	}
	
	/**
	 * Return the element the internal pointer currently points to.
	 * @return mixed Current element in the collection.
	 */
	public function current()
	{
		return self::$data[$this->hash]->data->current();
	}
	
	/**
	 * Advances the internal pointer one position.
	 * @return mixed Element in the next position.
	 */
	public function next()
	{
		return self::$data[$this->hash]->data->next();
	}
	
	/**
	 * Fetches the key the internal pointer currently points to.
	 * @return mixed Current element's key in the collection.
	 */
	public function key()
	{
		return self::$data[$this->hash]->data->key();
	}
	
	/**
	 * Checks whether the pointer is a valid position.
	 * @return bool Is the pointer in a valid position?
	 */
	public function valid()
	{
		return self::$data[$this->hash]->data->valid();
	}
	
	/**
	 * Set the internal pointer of the collection to its first element.
	 * @return mixed First element in collection.
	 */
	public function rewind()
	{
		return self::$data[$this->hash]->data->rewind();
	}
	
	/**
	 * Returns all data stored in collection.
	 * @return array All data stored in collection.
	 */
	public function all() : array
	{
		return self::$data[$this->hash]->data->all();
	}
	
	/**
	 * This configuration collection does not allow changes to its contents.
	 * Thus, all data given to collection by creation time, will be kept until
	 * instances no longer exist.
	 */
	public function clear() : array
	{
		return $this->all();
	}
	
	/**
	 * Copies all the content present in this object.
	 * @return static A new collection with copied data.
	 */
	public function copy()
	{
		return clone $this;
	}
	
	/**
	 * Checks whether collection is currently empty.
	 * @return bool Is collection empty?
	 */
	public function empty() : bool
	{
		return self::$data[$this->hash]->data->empty();
	}
	
	/**
	 * Counts the number of elements currently in collection.
	 * @return int Number of elements stored in the collection.
	 */
	public function count()
	{
		return self::$data[$this->hash]->data->count();
	}
	
}
