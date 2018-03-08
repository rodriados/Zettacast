<?php
/**
 * Zettacast\Helper\StorageInterface interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Helper;

interface StorageInterface
{
	/**
	 * Retrieve an element stored in object.
	 * @param mixed $key Key of requested element.
	 * @return mixed Requested element or null if not found.
	 */
	public function get($key);
	
	/**
	 * Check whether an element key is known.
	 * @param mixed $key Key to check if exists.
	 * @return bool Is key known to storage?
	 */
	public function has($key): bool;
	
	/**
	 * Create or update a value related to given key.
	 * @param mixed $key Key to create or update.
	 * @param mixed $value Value to store related to the given key.
	 */
	public function set($key, $value): void;
	
	/**
	 * Delete an element from storage, if it is known.
	 * @param mixed $key Key to remove.
	 */
	public function del($key): void;
}
