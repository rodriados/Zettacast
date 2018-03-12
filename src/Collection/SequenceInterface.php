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
	 * Gives access to first element in sequence.
	 * @return mixed Sequence's first element.
	 */
	public function first();
	
	/**
	 * Gives access to last element in sequence.
	 * @return mixed Sequence's last element.
	 */
	public function last();
	
	/**
	 * Pops an element out of the end of sequence.
	 * @return mixed Popped element.
	 */
	public function pop();
	
	/**
	 * Pushes an element onto the end of sequence.
	 * @param mixed $value Value to append onto sequence.
	 */
	public function push($value): void;
	
	/**
	 * Shifts a value off the beginning of sequence.
	 * @return mixed Shifted value.
	 */
	public function shift();
	
	/**
	 * Pushes an element onto the beginning of sequence.
	 * @param mixed $value Value to prepend onto sequence.
	 */
	public function unshift($value): void;
}
