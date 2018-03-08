<?php
/**
 * Zettacast\Helper\ListableInterface interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Helper;

interface ListableInterface extends ExtractableInterface, \Countable, \Iterator
{
	/**
	 * Clear the object and return its old contents.
	 * @return array Old object's contents before clearing.
	 */
	public function clear(): array;
	
	/**
	 * Check whether object is currently empty.
	 * @return bool Is object empty?
	 */
	public function empty(): bool;
	
	/**
	 * Give access to object's raw contents. That is, it exposes the internal
	 * content that is wrapped by object.
	 * @return array The raw sequence contents as array.
	 */
	public function raw(): array;
}
