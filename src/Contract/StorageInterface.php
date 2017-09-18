<?php
/**
 * Zettacast\Contract\StorageInterface interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Contract;

/**
 * Storage interface. This interface exposes the basic four operations that any
 * storage related object must implement.
 * @package Zettacast\Contract
 */
interface StorageInterface
{
	/**
	 * Retrieves an element stored in object.
	 * @param mixed $key Key of requested element.
	 * @return mixed Requested element or null if not found.
	 */
	public function get($key);
	
	/**
	 * Checks whether an element key is known.
	 * @param mixed $key Key to be check if exists.
	 * @return bool Is key known to storage?
	 */
	public function has($key): bool;
	
	/**
	 * Creates or updates a value related to the given key.
	 * @param mixed $key Key to created or updated.
	 * @param mixed $value Value to be stored related to the given key.
	 */
	public function set($key, $value);
	
	/**
	 * Deletes an element from storage, if it is known.
	 * @param mixed $key Key to be removed.
	 */
	public function del($key);
	
}
