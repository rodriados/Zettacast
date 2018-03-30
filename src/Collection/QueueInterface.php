<?php
/**
 * Zettacast\Collection\QueueInterface interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

use Zettacast\Support\IterableInterface;

interface QueueInterface extends IterableInterface
{
	/**
	 * Peeks at next node to be removed from sequence.
	 * @return mixed Peeked value from the next node in sequence.
	 */
	public function peek();
	
	/**
	 * Pops the next node in sequence.
	 * @return mixed Popped value from next position in sequence.
	 */
	public function pop();
	
	/**
	 * Pushes a value and places in on the last position in sequence.
	 * @param mixed $value Value to push to queue.
	 */
	public function push($value): void;
}
