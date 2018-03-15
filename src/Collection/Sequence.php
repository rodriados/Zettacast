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

/**
 * The sequence class. This class has methods appliable for all kinds of
 * sequences which keeps its elements ordered as given and allows them to be
 * taken out in both directions.
 * @package Zettacast\Collection
 * @version 1.0
 */
class Sequence implements SequenceInterface, \ArrayAccess
{
	use ArrayAccessTrait;
	
	/**
	 * Data to store.
	 * @var \SplDoublyLinkedList Data stored in sequence.
	 */
	protected $data;
	
	/**
	 * Sequence constructor.
	 * Stores given data in sequence.
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
	 * Sequence clone magic method.
	 * Clones the internal sequence structure so the cloned object can be
	 * totally independent from the original one.
	 */
	public function __clone()
	{
		$this->data = clone $this->data;
	}
	
	/**
	 * Accesses element stored in given index.
	 * @param int $index Index to access.
	 * @param mixed $default Default value as fallback.
	 * @return mixed Element stored in given index.
	 */
	public function get($index, $default = null)
	{
		return $this->has($index)
			? $this->data[$index]
			: $default;
	}
	
	/**
	 * Checks whether index exists.
	 * @param int $index Index to check existance.
	 * @return bool Does given index exist?
	 */
	public function has($index): bool
	{
		return isset($this->data[$index]);
	}
	
	/**
	 * Sets a value to given index.
	 * @param int $index Index to update.
	 * @param mixed $value Value to store in index.
	 */
	public function set($index, $value): void
	{
		if($index <= $this->count())
			$this->data[$index] = $value;
	}
	
	/**
	 * Removes an element from sequence.
	 * @param mixed $index Index to remove.
	 */
	public function del($index): void
	{
		if($this->has($index))
			unset($this->data[$index]);
	}
	
	/**
	 * Returns all data stored in sequence.
	 * @return array All data stored in sequence.
	 */
	public function raw(): array
	{
		return toarray($this->data);
	}
	
	/**
	 * Applies a callback to all values stored in sequence.
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
	 * Chunks the sequence into pieces of given size.
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
	 * Clears all data stored in object and returns it.
	 * @return array All data stored in sequence before clearing.
	 */
	public function clear(): array
	{
		$ref = $this->raw();
		$this->data = new \SplDoublyLinkedList;
		
		return $ref;
	}
	
	/**
	 * Counts the number of elements currently in sequence.
	 * @return int Number of elements stored in sequence.
	 */
	public function count(): int
	{
		return $this->data->count();
	}
	
	/**
	 * Returns the element the internal pointer currently points to.
	 * @return mixed Current element in sequence.
	 */
	public function current()
	{
		return $this->data->current();
	}
	
	/**
	 * Checks whether sequence is currently empty.
	 * @return bool Is sequence empty?
	 */
	public function empty(): bool
	{
		return $this->data->isEmpty();
	}
	
	/**
	 * Checks whether all elements in sequence pass given test.
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
	 * Creates a new sequence with all elements except the specified keys.
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
	 * Filters elements according to given test. If no test function is given,
	 * it fallbacks to removing all false values.
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
	 * Returns first element in sequence.
	 * @return mixed Sequence's first element.
	 */
	public function first()
	{
		return $this->data->bottom();
	}
	
	/**
	 * Intersects given items with sequence's elements.
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
	 * Creates a generator that iterates over sequence.
	 * @yield mixed Sequence's stored values.
	 */
	public function iterate(): \Generator
	{
		yield from $this->data;
	}
	
	/**
	 * Fetches key the internal pointer currently points to.
	 * @return int Current element's key in sequence.
	 */
	public function key(): int
	{
		return $this->data->key();
	}
	
	/**
	 * Returns last element in the sequence.
	 * @return mixed Sequence's last element.
	 */
	public function last()
	{
		return $this->data->top();
	}
	
	/**
	 * Creates a new sequence, the same type as the original, by using a
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
	 * Merges given items into sequence's elements.
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
	 * Advances the internal pointer one position.
	 * @return mixed Element in next position.
	 */
	public function next()
	{
		$this->data->next();
		return $this->current();
	}
	
	/**
	 * Creates a new sequence with a subset of elements.
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
	 * Passes sequence to given function and returns the result.
	 * @param callable $fn Function to which sequence is passed to.
	 * @return mixed Callback's return result.
	 */
	public function pipe(callable $fn)
	{
		return $fn($this);
	}
	
	/**
	 * Pops an element out of top of sequence.
	 * @return mixed Popped element.
	 */
	public function pop()
	{
		return $this->data->pop();
	}
	
	/**
	 * Rewinds the internal pointer one position.
	 * @return mixed Element in previous position.
	 */
	public function prev()
	{
		$this->data->prev();
		return $this->data->current();
	}
	
	/**
	 * Pulls an element out of given index.
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
	 * Pushes an element onto end of sequence.
	 * @param mixed $value Value to append onto sequence.
	 */
	public function push($value): void
	{
		$this->data->push($value);
	}
	
	/**
	 * Returns randomly selected elements from sequence. If the amount
	 * requested is larger than the sequence itself, return value will simply
	 * be shuffled sequence and will not have the requested amount of items.
	 * @param int $sample Amount of elements to be selected.
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
	 * Reduces sequence to a single value calculated by callback.
	 * @param callable $fn Reducing function. Parameters: carry, value.
	 * @param mixed $initial Initial reduction value.
	 * @return mixed Resulting value from reduction.
	 */
	public function reduce(callable $fn, $initial = null)
	{
		return array_reduce($this->raw(), $fn, $initial);
	}
	
	/**
	 * Returns a reversed copy of sequence.
	 * @return static Reversed sequence.
	 */
	public function reverse()
	{
		return $this->new(array_reverse($this->raw()));
	}
	
	/**
	 * Sets the internal pointer of sequence to its first element.
	 * @return mixed First element in sequence.
	 */
	public function rewind()
	{
		$this->data->rewind();
		return $this->data->current();
	}
	
	/**
	 * Rotates sequence by a given number of rotations. If number of rotations
	 * is positive, the elements in bottom of sequence will rotate to top. But,
	 * if the number of rotations is negative, the elements in top of sequence
	 * will rotate to bottom.
	 * @param int $rotations Number of rotations to perform.
	 * @return static New rotated sequence.
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
	 * Attemps to find index of an element stored in sequence.
	 * @param mixed $needle Value to be found.
	 * @param bool $strict Should the search be for identical elements?
	 * @return int|bool Index of the found element, or false otherwise.
	 */
	public function search($needle, bool $strict = false)
	{
		return array_search($needle, $this->raw(), $strict);
	}
	
	/**
	 * Shifts a value off the bottom of sequence.
	 * @return mixed Shifted value.
	 */
	public function shift()
	{
		return $this->data->shift();
	}
	
	/**
	 * Shuffles sequence to an unknown order.
	 * @return static Shuffled sequence.
	 */
	public function shuffle()
	{
		$data = $this->raw();
		shuffle($data);
		
		return $this->new($data);
	}
	
	/**
	 * Retrieves a slice of sequence.
	 * @param int $index Slice initial position.
	 * @param int $length Length of slice.
	 * @return static Sliced sequence.
	 */
	public function slice(int $index, int $length = null)
	{
		return $this->new(array_slice($this->raw(), $index, $length));
	}
	
	/**
	 * Sorts the sequence using a given function.
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
	 * Removes part of sequence and replaces it.
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
	 * Splits sequence into given number of groups.
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
	 * Takes first or last specified number of items.
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
	 * Passes sequence to given function and returns it.
	 * @param callable $fn Function to which sequence is passed to.
	 * @return static Sequence's copy sent to function.
	 */
	public function tap(callable $fn)
	{
		$fn($copy = clone $this);
		return $copy;
	}
	
	/**
	 * Pushes an element onto the beginning of sequence.
	 * @param mixed $value Value to prepend onto sequence.
	 */
	public function unshift($value): void
	{
		$this->data->unshift($value);
	}
	
	/**
	 * Checks whether the pointer is a valid position.
	 * @return bool Is the pointer in a valid position?
	 */
	public function valid(): bool
	{
		return $this->data->valid();
	}
	
	/**
	 * Iterates over sequence and executes a function over every element.
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
