<?php
/**
 * Zettacast\Collection\SequenceInterface interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

interface SequenceInterface extends CollectionInterface
{
	/**
	 * Give access to the first element in the sequence.
	 * @return mixed Sequence's first element.
	 */
	public function first();
	
	/**
	 * Give access to the last element in the sequence.
	 * @return mixed Sequence's last element.
	 */
	public function last();
	
	/**
	 * Pop an element out of the top of the sequence.
	 * @return mixed Popped element.
	 */
	public function pop();
	
	/**
	 * Push an element onto the end of the sequence.
	 * @param mixed $value Value to be appended onto the sequence.
	 */
	public function push($value): void;
	
	/**
	 * Shift a value off the bottom of the sequence.
	 * @return mixed Shifted value.
	 */
	public function shift();
	
	/**
	 * Push an element onto the beginning of the sequence.
	 * @param mixed $value Value to be prepended onto the sequence.
	 */
	public function unshift($value): void;
}
