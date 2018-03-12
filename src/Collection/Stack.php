<?php
/**
 * Zettacast\Collection\Stack class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

/**
 * The stack class has methods appliable for all kinds of stacks. Only integer
 * key types are acceptable in stacks.
 * @package Zettacast\Collection
 * @version 1.0
 */
class Stack extends Queue
{
	/**
	 * Stack constructor.
	 * This constructor simply creates a new base for all of this object's data
	 * to be stored on.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->lifo();
	}
	
	/**
	 * Clears all data stored in object and returns it.
	 * @return array All data stored in collection before clearing.
	 */
	public function clear(): array
	{
		$old = parent::clear();
		$this->lifo();
		
		return $old;
	}
	
	/**
	 * Peeks at node on the top of stack.
	 * @return mixed Peeked value from stack's top position.
	 */
	public function peek()
	{
		return !$this->empty()
			? $this->data->top()
			: null;
	}
	
	/**
	 * Pops node from the top of stack and returns it.
	 * @return mixed Popped value from stack's top position.
	 */
	public function pop()
	{
		return !$this->empty()
			? $this->data->pop()
			: null;
	}
	
	/**
	 * Forces data to iterate in last-in-first-out mode, so the internal queue
	 * becomes a stack.
	 */
	private function lifo()
	{
		$this->data->setIteratorMode(\SplDoublyLinkedList::IT_MODE_LIFO);
	}
}
