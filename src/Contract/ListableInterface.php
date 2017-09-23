<?php
/**
 * Zettacast\Contract\ListableInterface interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Contract;

/**
 * Listable interface. This interface exposes the minimal methods needed for
 * the creation of a listable object.
 * @package Zettacast\Contract
 */
interface ListableInterface extends ExtractableInterface, \Countable, \Iterator
{
	/**
	 * Gives access to the object's raw contents. That is, it exposes the
	 * internal content that is wrapped by the object.
	 * @return array The raw sequence contents as array.
	 */
	public function raw(): array;
	
	/**
	 * Clears the object and returns its old contents.
	 * @return array Old object's contents before clearing.
	 */
	public function clear(): array;
	
	/**
	 * Clones the object with all of its contents.
	 * @return $this A new cloned object.
	 */
	public function copy();
	
	/**
	 * Checks whether object is currently empty.
	 * @return bool Is object empty?
	 */
	public function empty(): bool;
	
}
