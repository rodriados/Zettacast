<?php
/**
 * Zettacast\Collection\Queue class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

/**
 * The queue class. This class has methods that allow the creation of a queue,
 * that is, a sequence of elements following the first-in-first-out protocol.
 * @package Zettacast\Collection
 * @version 1.0
 */
class Queue implements QueueInterface
{
	/**
	 * Data to store.
	 * @var \SplDoublyLinkedList Data stored in queue.
	 */
	protected $data;
	
	/**
	 * Queue constructor.
	 * Sets up the internal queue structure.
	 */
	public function __construct()
	{
		$this->data = new \SplDoublyLinkedList;
	}
	
	/**
	 * Queue clone magic method.
	 * Clones the internal queue structure so the cloned object can be totally
	 * independent from the original one.
	 */
	public function __clone()
	{
		$this->data = clone $this->data;
	}
	
	/**
	 * Returns all data stored in queue.
	 * @return array All data stored in queue.
	 */
	public function raw(): array
	{
		return toarray($this->data);
	}
	
	/**
	 * Clears all data stored in object and returns it.
	 * @return array All data stored in queue before clearing.
	 */
	public function clear(): array
	{
		$old = $this->raw();
		$this->data = new \SplDoublyLinkedList;
		
		return $old;
	}
	
	/**
	 * Counts the number of elements currently in queue.
	 * @return int Number of queued elements.
	 */
	public function count(): int
	{
		return $this->data->count();
	}
	
	/**
	 * Returns the element the internal pointer currently points to.
	 * @return mixed Current element in queue.
	 */
	public function current()
	{
		return $this->data->current();
	}
	
	/**
	 * Checks whether queue is currently empty.
	 * @return bool Is queue empty?
	 */
	public function empty(): bool
	{
		return $this->data->isEmpty();
	}
	
	/**
	 * Fetches key the internal pointer currently points to.
	 * @return int Current element's key in queue.
	 */
	public function key(): int
	{
		return $this->data->key();
	}
	
	/**
	 * Advances the internal pointer by one position.
	 * @return mixed Element in the next position.
	 */
	public function next()
	{
		$this->data->next();
		return $this->data->current();
	}
	
	/**
	 * Peeks at node on the bottom of queue.
	 * @return mixed Peeked value from queue's bottom position.
	 */
	public function peek()
	{
		return !$this->empty()
			? $this->data->bottom()
			: null;
	}
	
	/**
	 * Pops node from the bottom of queue and returns it.
	 * @return mixed Popped value from queue's bottom position.
	 */
	public function pop()
	{
		return !$this->empty()
			? $this->data->shift()
			: null;
	}
	
	/**
	 * Rewinds the internal pointer one position.
	 * @return mixed Element in the previous position.
	 */
	public function prev()
	{
		$this->data->prev();
		return $this->data->current();
	}
	
	/**
	 * Pushes a value and places it on top of queue.
	 * @param mixed $value Value to push to queue.
	 */
	public function push($value): void
	{
		$this->data->push($value);
	}
	
	/**
	 * Sets the internal pointer of queue to its first element.
	 * @return mixed First element in sequence.
	 */
	public function rewind()
	{
		$this->data->rewind();
		return $this->data->current();
	}
	
	/**
	 * Checks whether the pointer is a valid position.
	 * @return bool Is the pointer in a valid position?
	 */
	public function valid(): bool
	{
		return $this->data->valid();
	}
}
