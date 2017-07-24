<?php
/**
 * Zettacast\Contract\Collection\Sequence interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Contract\Collection;

interface Sequence
	extends Listable
{
	/**
	 * Returns the first element in the sequence.
	 * @return mixed Sequence's first element.
	 */
	public function first();
	
	/**
	 * Accesses the element stored in the given index.
	 * @param int $index Index to be accessed.
	 * @param mixed $default Default value as fallback.
	 * @return mixed Element stored in given index.
	 */
	public function get(int $index, $default = null);
	
	/**
	 * Checks whether the index exists.
	 * @param int $index Index to be checked.
	 * @return bool Does given index exist?
	 */
	public function has(int $index) : bool;
	
	/**
	 * Returns the last element in the sequence.
	 * @return mixed Sequence's last element.
	 */
	public function last();
	
	/**
	 * Pops an element out of the top of the sequence.
	 * @return mixed Popped element.
	 */
	public function pop();
	
	/**
	 * Pushes an element to the top of the sequence.
	 * @param mixed $value Element to be pushed to the top of the sequence.
	 * @return static Sequence for method chaining.
	 */
	public function push($value);
	
	/**
	 * Removes an element from sequence.
	 * @param mixed $index Index to be removed.
	 * @return static Sequence for method chaining.
	 */
	public function remove(int $index);
	
	/**
	 * Sets a value to the given index.
	 * @param int $index Index to be updated.
	 * @param mixed $value Value to be stored in index.
	 */
	public function set(int $index, $value);
	
	/**
	 * Shifts a value off the bottom of the sequence.
	 * @return mixed Shifted value.
	 */
	public function shift();
	
	/**
	 * Pushes an element onto the beginning of the sequence.
	 * @param mixed $value Value to be prepended onto the sequence.
	 * @return static Sequence for method chaining.
	 */
	public function unshift($value);
	
}
