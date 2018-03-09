<?php
/**
 * Zettacast\Collection\Stack class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

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
	 * @inheritdoc
	 */
	public function clear(): array
	{
		$old = parent::clear();
		$this->lifo();
		
		return $old;
	}
	
	/**
	 * @inheritdoc
	 */
	public function peek()
	{
		return !$this->empty()
			? $this->data->top()
			: null;
	}
	
	/**
	 * @inheritdoc
	 */
	public function pop()
	{
		return !$this->empty()
			? $this->data->pop()
			: null;
	}
	
	/**
	 * Force the data to be iterated in the "Last In, First Out" mode, so the
	 * internal queue is transformed into a stack.
	 */
	private function lifo()
	{
		$this->data->setIteratorMode(\SplDoublyLinkedList::IT_MODE_LIFO);
	}
}
