<?php
/**
 * Zettacast\Collection\Queue class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

/**
 * Queue class. This class has methods appliable for all kinds of queues. Only
 * integer key types are acceptable.
 * @package Zettacast\Collection
 * @version 1.0
 */
class Queue implements QueueInterface
{
	
	/**
	 * Data to be stored.
	 * @var \SplDoublyLinkedList Data stored in queue.
	 */
	protected $data;
	
	/**
	 * Queue constructor. This constructor simply creates a new base for all
	 * of this object's data to be stored on.
	 */
	public function __construct()
	{
		$this->data = new \SplDoublyLinkedList;
	}
	
	/**
	 * Clone magic method. This method allows a correct cloning of this
	 * object's contents, so it does not interfere with the original object.
	 */
	public function __clone()
	{
		$this->data = clone $this->data;
	}
	
	/**
	 * Returns all data stored in queue.
	 * @return array All data stored in queue.
	 */
	public function all(): array
	{
		return toArray($this->data);
	}
	
	/**
	 * Clears all data stored in object and returns it.
	 * @return array All data stored in queue before clearing.
	 */
	public function clear(): array
	{
		$old = $this->all();
		$this->data = new \SplDoublyLinkedList;
		
		return $old;
	}
	
	/**
	 * Copies all the content present in this object.
	 * @return static A new queue with copied data.
	 */
	public function copy()
	{
		return clone $this;
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
	 * Return the element the internal pointer currently points to.
	 * @return mixed Current element in the queue.
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
	 * Fetches the key the internal pointer currently points to.
	 * @return int Current element's key in the queue.
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
	 * Peeks at the node that is on the bottom of the queue.
	 * @return mixed Peeked value from queue's bottom position.
	 */
	public function peek()
	{
		return $this->data->bottom();
	}
	
	/**
	 * Pops the node that is on the bottom of the queue and returns it.
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
	 * Pushes a value and positions it on the top of the queue.
	 * @param mixed $value Value to be pushed to queue.
	 * @return $this Queue for method chaining.
	 */
	public function push($value)
	{
		$this->data->push($value);
		return $this;
	}
	
	/**
	 * Set the internal pointer of the queue to its first element.
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
