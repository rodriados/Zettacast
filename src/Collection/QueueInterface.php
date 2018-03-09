<?php
/**
 * Zettacast\Collection\QueueInterface interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

use Zettacast\Helper\ListableInterface;

interface QueueInterface extends ListableInterface
{
	/**
	 * Peek at the next node to remove from sequence.
	 * @return mixed Peeked value from the next node in sequence.
	 */
	public function peek();
	
	/**
	 * Pop node that is the next in line out from sequence.
	 * @return mixed Popped value from next position in sequence.
	 */
	public function pop();
	
	/**
	 * Push a value and place it in one of the ends of the queue.
	 * @param mixed $value Value to be pushed to queue.
	 */
	public function push($value): void;
}
