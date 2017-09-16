<?php
/**
 * Zettacast\Collection\Stack class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

/**
 * Stack class. This class has methods appliable for all kinds of stacks. Only
 * integer key types are acceptable.
 * @package Zettacast\Collection
 * @version 1.0
 */
class Stack extends Queue
{
	/**
	 * Stack constructor. This constructor simply creates a new base for all
	 * of this object's data to be stored on.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->forceLIFO();
	}
	
	/**
	 * Clears all data stored in object and returns it.
	 * @return array All data stored in collection before clearing.
	 */
	public function clear(): array
	{
		$old = parent::clear();
		$this->forceLIFO();
		
		return $old;
	}
	
	/**
	 * Peeks at the node that is on the top of the stack.
	 * @return mixed Peeked value from stack's top position.
	 */
	public function peek()
	{
		return $this->data->top();
	}
	
	/**
	 * Pops the node that is on the top of the stack and returns it.
	 * @return mixed Popped value from stack's top position.
	 */
	public function pop()
	{
		return !$this->empty()
			? $this->data->pop()
			: null;
	}
	
	/**
	 * Forces the data to be iterated in the "Last In, First Out" mode, so the
	 * internal queue is transformed into a stack.
	 */
	private function forceLIFO()
	{
		$this->data->setIteratorMode(\SplDoublyLinkedList::IT_MODE_LIFO);
	}
	
}
