<?php
/**
 * Zettacast\Collection\Sequence class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

use Zettacast\Helper\ArrayAccessTrait;

class Sequence implements SequenceInterface, \ArrayAccess
{
	use ArrayAccessTrait;
	
	/**
	 * Data to be stored.
	 * @var \SplDoublyLinkedList Data stored in sequence.
	 */
	protected $data;
	
	/**
	 * Sequence constructor.
	 * This constructor simply creates a new base for all of this object's data
	 * to be stored on.
	 * @param array|\Traversable $data Data to store as a sequence.
	 */
	public function __construct($data = null)
	{
		$data = !is_null($data)
			? toarray($data)
			: [];
		
		$this->data = new \SplDoublyLinkedList;
		
		foreach($data as $value)
			$this->data->push($value);
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
	public function get($index, $default = null)
	{
		return $this->has($index)
			? $this->data[$index]
			: $default;
	}
	
	/**
	 * @inheritdoc
	 */
	public function has($index): bool
	{
		return isset($this->data[$index]);
	}
	
	/**
	 * @inheritdoc
	 */
	public function set($index, $value): void
	{
		if($index <= $this->count())
			$this->data->add($index, $value);
	}
	
	/**
	 * @inheritdoc
	 */
	public function del($index): void
	{
		if($this->has($index))
			unset($this->data[$index]);
	}
	
	/**
	 * @inheritdoc
	 */
	public function raw(): array
	{
		return toarray($this->data);
	}
	
	/**
	 * Apply a callback to all values stored in sequence.
	 * @param callable $fn Callback to apply. Parameters: value, key.
	 * @param mixed|mixed[] $userdata Optional extra parameters for function.
	 * @return static Sequence for method chaining.
	 */
	public function apply(callable $fn, $userdata = null)
	{
		foreach($this->iterate() as $key => $value)
			$this->data[$key] = $fn($value, $key, ...toarray($userdata));
		
		return $this;
	}
	
	/**
	 * Chunk the sequence into pieces of given size.
	 * @param int $size Size of chunks.
	 * @return static[] Array of sequence chunks.
	 */
	public function chunk(int $size): array
	{
		if($size <= 0)
			return [];
		
		foreach(array_chunk($this->raw(), $size) as $sequence)
			$chunk[] = $this->new($sequence);
		
		return $chunk ?? [];
	}
	
	/**
	 * @inheritdoc
	 */
	public function clear(): array
	{
		$ref = $this->raw();
		$this->data = new \SplDoublyLinkedList;
		
		return $ref;
	}
	
	/**
	 * Count the number of elements currently in sequence.
	 * @return int Number of elements stored in sequence.
	 */
	public function count(): int
	{
		return $this->data->count();
	}
	
	/**
	 * Return the element the internal pointer currently points to.
	 * @return mixed Current element in sequence.
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
	 * Check whether all elements in sequence pass given test.
	 * @param callable $fn Test function. Parameters: value, key.
	 * @return bool Does every element pass the tests?
	 */
	public function every(callable $fn = null): bool
	{
		$fn = $fn ?: 'with';
		
		foreach($this->iterate() as $key => $value)
			if(!$fn($value, $key))
				return false;
		
		return true;
	}
	
	/**
	 * Create a new sequence with all elements except the specified keys.
	 * @param int|int[] $keys Keys to forget in new sequence.
	 * @return static New sequence instance.
	 */
	public function except($keys)
	{
		return $this->new(
			array_diff_key($this->raw(), array_flip(toarray($keys)))
		);
	}
	
	/**
	 * Filter elements according to given test. If no test function is given,
	 * it fallbacks to removing all false equivalent values.
	 * @param callable $fn Test function. Parameters: value, key.
	 * @return static Sequence of all filtered values.
	 */
	public function filter(callable $fn = null)
	{
		return $this->new(is_null($fn)
			? array_filter($this->raw())
			: array_filter($this->raw(), $fn, ARRAY_FILTER_USE_BOTH)
		);
	}
	
	/**
	 * @inheritdoc
	 */
	public function first()
	{
		return $this->data->bottom();
	}
	
	/**
	 * Intersect given items with sequence's elements.
	 * @param array|Sequence $items Items to intersect with sequence.
	 * @return static Sequence of intersected elements.
	 */
	public function intersect($items)
	{
		return $this->new(
			array_intersect($this->raw(), toarray($items))
		);
	}
	
	/**
	 * Create a generator that iterates over the sequence.
	 * @yield mixed Sequence's stored values.
	 */
	public function iterate(): \Generator
	{
		yield from $this->data;
	}
	
	/**
	 * Fetch the key the internal pointer currently points to.
	 * @return int Current element's key in sequence.
	 */
	public function key(): int
	{
		return $this->data->key();
	}
	
	/**
	 * @inheritdoc
	 */
	public function last()
	{
		return $this->data->top();
	}
	
	/**
	 * Create a new sequence, the same type as the original, by using a
	 * function for creating the new elements based on the older ones. The
	 * callback receives the following parameters respectively: value, key.
	 * @param callable $fn Function to use for creating new elements.
	 * @return static New sequence instance.
	 */
	public function map(callable $fn)
	{
		$target = $this->raw();
		
		return $this->new(
			array_map($fn, $target, array_keys($target))
		);
	}
	
	/**
	 * Merge given items into sequence's elements.
	 * @param mixed $items Items to merge into sequence.
	 * @return static Sequence of all merged elements.
	 */
	public function merge($items)
	{
		return $this->new(
			array_merge($this->raw(), array_values($items))
		);
	}
	
	/**
	 * Advance the internal pointer one position.
	 * @return mixed Element in next position.
	 */
	public function next()
	{
		$this->data->next();
		return $this->current();
	}
	
	/**
	 * Create a new sequence with a subset of elements.
	 * @param int|int[] $keys Keys to include in new sequence.
	 * @return static New sequence instance.
	 */
	public function only($keys)
	{
		return $this->new(
			array_intersect_key($this->raw(), array_flip(toarray($keys)))
		);
	}
	
	/**
	 * Pass the sequence to given function and return the result.
	 * @param callable $fn Function to which sequence is passed to.
	 * @return mixed Callback's return result.
	 */
	public function pipe(callable $fn)
	{
		return $fn($this);
	}
	
	/**
	 * @inheritdoc
	 */
	public function pop()
	{
		return $this->data->pop();
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
	 * Pull an element out of given index.
	 * @param int $index Index to pull off.
	 * @param mixed $default Default value if index not found.
	 * @return mixed Pulled element.
	 */
	public function pull(int $index, $default = null)
	{
		$value = $this->get($index, $default);
		$this->del($index);
		
		return $value;
	}
	
	/**
	 * @inheritdoc
	 */
	public function push($value): void
	{
		$this->data->push($value);
	}
	
	/**
	 * Return randomly selected elements from sequence. If the amount
	 * requested is larger than the sequence itself, return value will simply
	 * be shuffled sequence and will not have the requested amount of items.
	 * @param int $sample Amount of elements to select.
	 * @return static Randomly selected elements from sequence.
	 */
	public function random(int $sample = 1)
	{
		if($sample >= $this->count())
			return $this->shuffle();
		
		$keys = array_rand($this->raw(), $sample);
		return $this->only($keys);
	}
	
	/**
	 * Reduce sequence to a single value calculated by callback.
	 * @param callable $fn Reducing function. Parameters: carry, value.
	 * @param mixed $initial Initial reduction value.
	 * @return mixed Resulting value from reduction.
	 */
	public function reduce(callable $fn, $initial = null)
	{
		return array_reduce($this->raw(), $fn, $initial);
	}
	
	/**
	 * Return a reversed copy of the sequence.
	 * @return static Reversed sequence.
	 */
	public function reverse()
	{
		return $this->new(array_reverse($this->raw()));
	}
	
	/**
	 * Set the internal pointer of sequence to its first element.
	 * @return mixed First element in sequence.
	 */
	public function rewind()
	{
		$this->data->rewind();
		return $this->data->current();
	}
	
	/**
	 * Rotate the sequence by a given number of rotations. If the number of
	 * rotations is positive, the elements in the bottom of the sequence will
	 * be rotated to the top. But, if the number of rotations is negative, the
	 * elements in the top of the sequence will be rotate to the bottom.
	 * @param int $rotations Number of rotations to perform.
	 * @return static New rotate sequence.
	 */
	public function rotate(int $rotations = 1)
	{
		$btt = $rotations > 0;
		$rot = (abs($rotations) % $this->count()) * ($btt ? -1 : 1);
		$arr = $this->raw();
		
		return $this->new(
			array_merge(array_slice($arr, $rot), array_slice($arr, 0, $rot))
		);
	}
	
	/**
	 * Attemp to find the index of an element stored in sequence.
	 * @param mixed $needle Value to be found.
	 * @param bool $strict Should the search be for identical elements?
	 * @return int|bool Index of the found element, or false otherwise.
	 */
	public function search($needle, bool $strict = false)
	{
		return array_search($needle, $this->raw(), $strict);
	}
	
	/**
	 * @inheritdoc
	 */
	public function shift()
	{
		return $this->data->shift();
	}
	
	/**
	 * Shuffle the sequence to an unknown order.
	 * @return static Shuffled sequence.
	 */
	public function shuffle()
	{
		$data = $this->raw();
		shuffle($data);
		
		return $this->new($data);
	}
	
	/**
	 * Retrieve a slice of sequence.
	 * @param int $index Slice initial position.
	 * @param int $length Length of slice.
	 * @return static Sliced sequence.
	 */
	public function slice(int $index, int $length = null)
	{
		return $this->new(array_slice($this->raw(), $index, $length));
	}
	
	/**
	 * Sort the sequence using a given function.
	 * @param callable $fn Ordering function.
	 * @return static Sorted sequence.
	 */
	public function sort(callable $fn = null)
	{
		$data = $this->raw();
		is_null($fn) ? sort($data) : usort($data, $fn);
		
		return $this->new($data);
	}
	
	/**
	 * Remove part of sequence and replaces it.
	 * @param int $offset Initial splice offset.
	 * @param int $length Length of splice portion.
	 * @param mixed $replace Replacement for removed slice.
	 * @return static Spliced sequence.
	 */
	public function splice(int $offset, int $length = null, $replace = [])
	{
		return $this->new(array_splice(
			toarray($this->data),
			$offset,
			$length ?: $this->count(),
			$replace
		));
	}
	
	/**
	 * Split sequence into given number of groups.
	 * @param int $count Number of groups to split sequence.
	 * @return static[] Splitted sequence.
	 */
	public function split(int $count): array
	{
		return !$this->empty()
			? $this->chunk(ceil($this->count() / $count))
			: [];
	}
	
	/**
	 * Take the first or last specified number of items.
	 * @param int $limit Number of items to take.
	 * @return static Sequence of taken items.
	 */
	public function take(int $limit)
	{
		return $limit < 0
			? $this->slice($limit, $this->count())
			: $this->slice(0, $limit);
	}
	
	/**
	 * Pass sequence to given function and return it.
	 * @param callable $fn Function to which sequence is passed to.
	 * @return static Sequence's copy sent to function.
	 */
	public function tap(callable $fn)
	{
		$fn($copy = clone $this);
		return $copy;
	}
	
	/**
	 * @inheritdoc
	 */
	public function unshift($value): void
	{
		$this->data->unshift($value);
	}
	
	/**
	 * Check whether the pointer is a valid position.
	 * @return bool Is the pointer in a valid position?
	 */
	public function valid(): bool
	{
		return $this->data->valid();
	}
	
	/**
	 * Iterate over sequence and executes a function over every element.
	 * @param callable $fn Iteration function. Parameters: value, key.
	 * @param mixed|mixed[] $userdata Optional extra parameters for function.
	 * @return static Sequence for method chaining.
	 */
	public function walk(callable $fn, $userdata = null)
	{
		foreach($this->iterate() as $key => $value)
			$fn($value, $key, ...toarray($userdata));
		
		return $this;
	}
	
	/**
	 * Creates a new instance of class based on an already existing instance.
	 * @param mixed $target Data to be fed into the new instance.
	 * @return static The new instance.
	 */
	protected function new($target = [])
	{
		return new static($target);
	}
}
