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
