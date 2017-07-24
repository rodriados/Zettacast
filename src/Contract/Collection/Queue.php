<?php
/**
 * Zettacast\Contract\Collection\Queue interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Contract\Collection;

/**
 * Queue interface. This interface exposes all methods needed for a class
 * to work as a queue.
 * @package Zettacast\Contract\Collection
 */
interface Queue
	extends Listable
{
	/**
	 * Peeks at the node that is on the top of the queue.
	 * @return mixed Peeked value from queue's top position.
	 */
	public function peek();
	
	/**
	 * Pops the node that is on the top of the queue and returns it.
	 * @return mixed Popped value from queue's top position.
	 */
	public function pop();
	
	/**
	 * Pushes a value and positions it in one of the ends of the queue.
	 * @param mixed $value Value to be pushed to queue.
	 */
	public function push($value);
	
}
