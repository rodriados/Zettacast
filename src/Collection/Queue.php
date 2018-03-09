<?php
/**
 * Zettacast\Collection\Queue class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

class Queue implements QueueInterface
{
	/**
	 * Data to be stored.
	 * @var \SplDoublyLinkedList Data stored in queue.
	 */
	protected $data;
	
	/**
	 * Queue constructor.
	 * This constructor simply creates a new base for all of this object's data
	 * to be stored on.
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
	 * @inheritdoc
	 */
	public function raw(): array
	{
		return toarray($this->data);
	}
	
	/**
	 * @inheritdoc
	 */
	public function clear(): array
	{
		$old = $this->raw();
		$this->data = new \SplDoublyLinkedList;
		
		return $old;
	}
	
	/**
	 * Count the number of elements currently in queue.
	 * @return int Number of queued elements.
	 */
	public function count(): int
	{
		return $this->data->count();
	}
	
	/**
	 * Return the element the internal pointer currently points to.
	 * @return mixed Current element in queue.
	 */
	public function current()
	{
		return $this->data->current();
	}
	
	/**
	 * @inheritdoc
	 */
	public function empty(): bool
	{
		return $this->data->isEmpty();
	}
	
	/**
	 * Fetch the key the internal pointer currently points to.
	 * @return int Current element's key in queue.
	 */
	public function key(): int
	{
		return $this->data->key();
	}
	
	/**
	 * Advance the internal pointer by one position.
	 * @return mixed Element in the next position.
	 */
	public function next()
	{
		$this->data->next();
		return $this->data->current();
	}
	
	/**
	 * @inheritdoc
	 */
	public function peek()
	{
		return !$this->empty()
			? $this->data->bottom()
			: null;
	}
	
	/**
	 * @inheritdoc
	 */
	public function pop()
	{
		return !$this->empty()
			? $this->data->shift()
			: null;
	}
	
	/**
	 * Rewind the internal pointer one position.
	 * @return mixed Element in the previous position.
	 */
	public function prev()
	{
		$this->data->prev();
		return $this->data->current();
	}
	
	/**
	 * @inheritdoc
	 */
	public function push($value): void
	{
		$this->data->push($value);
	}
	
	/**
	 * Set the internal pointer of queue to its first element.
	 * @return mixed First element in queue.
	 */
	public function rewind()
	{
		$this->data->rewind();
		return $this->data->current();
	}
	
	/**
	 * Check whether the pointer is a valid position.
	 * @return bool Is the pointer in a valid position?
	 */
	public function valid(): bool
	{
		return $this->data->valid();
	}
}
