<?php
/**
 * Zettacast\Contract\Collection\SequenceInterface interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Contract\Collection;

/**
 * Sequence interface. This interface exposes all methods needed for a class
 * to work as a sequence.
 * @package Zettacast\Collection
 */
interface SequenceInterface extends CollectionInterface
{
	/**
	 * Returns the first element in the sequence.
	 * @return mixed Sequence's first element.
	 */
	public function first();
	
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
	 * Pushes an element onto the end of the sequence.
	 * @param mixed $value Value to be appended onto the sequence.
	 */
	public function push($value);
	
	/**
	 * Shifts a value off the bottom of the sequence.
	 * @return mixed Shifted value.
	 */
	public function shift();
	
	/**
	 * Pushes an element onto the beginning of the sequence.
	 * @param mixed $value Value to be prepended onto the sequence.
	 */
	public function unshift($value);
	
}
