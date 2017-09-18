<?php
/**
 * Zettacast\Collection\Sequence class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Collection;

use Zettacast\Contract\ArrayAccessTrait;
use Zettacast\Contract\Collection\SequenceInterface;

/**
 * Sequence class. This class has methods appliable for all kinds of sequences.
 * Only integer and sequencial keys are acceptable.
 * @package Zettacast\Collection
 * @version 1.0
 */
class Sequence implements SequenceInterface, \ArrayAccess
{
	use ArrayAccessTrait;
	
	/**
	 * Data to be stored.
	 * @var \SplDoublyLinkedList Data stored in sequence.
	 */
	protected $data;
	
	/**
	 * Sequence constructor. This constructor simply creates a new base for all
	 * of this object's data to be stored on.
	 * @param array|\Traversable $data to be stored as a sequence.
	 */
	public function __construct($data = null)
	{
		$data = !is_null($data)
			? toArray($data)
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
	 * Accesses the element stored in the given index.
	 * @param int $index Index to be accessed.
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
	 * Checks whether the index exists.
	 * @param int $index Index to be checked.
	 * @return bool Does given index exist?
	 */
	public function has($index): bool
	{
		return isset($this->data[$index]);
	}
	
	/**
	 * Sets a value to the given index.
	 * @param int $index Index to be updated.
	 * @param mixed $value Value to be stored in index.
	 * @return $this Sequence for method chaining.
	 */
	public function set($index, $value)
	{
		if($index <= $this->count())
			$this->data->add($index, $value);
		
		return $this;
	}
	
	/**
	 * Removes an element from sequence.
	 * @param mixed $index Index to be removed.
	 * @return $this Sequence for method chaining.
	 */
	public function del($index)
	{
		if($this->has($index))
			unset($this->data[$index]);
		
		return $this;
	}
	
	/**
	 * Returns all data stored in sequence.
	 * @return array All data stored in sequence.
	 */
	public function all(): array
	{
		return toArray($this->data);
	}
	
	/**
	 * Applies a callback to all values stored in sequence.
	 * @param callable $fn Callback to be applied. Parameters: value, key.
	 * @param mixed|mixed[] $userdata Optional extra parameters for function.
	 * @return $this Sequence for method chaining.
	 */
	public function apply(callable $fn, $userdata = null)
	{
		$userdata = toArray($userdata);
		
		foreach($this->iterate() as $key => $value)
			$this->data[$key] = $fn($value, $key, ...$userdata);
		
		return $this;
	}
	
	/**
	 * Chunks the sequence into pieces of the given size.
	 * @param int $size Size of the chunks.
	 * @return array Array of sequence chunks.
	 */
	public function chunk(int $size): array
	{
		if($size <= 0)
			return [];
		
		foreach(array_chunk($this->all(), $size) as $sequence)
			$chunk[] = $this->new($sequence);
		
		return $chunk ?? [];
	}
	
	/**
	 * Clears all data stored in object and returns it.
	 * @return array All data stored in sequence before clearing.
	 */
	public function clear(): array
	{
		$ref = $this->all();
		$this->data = new \SplDoublyLinkedList;
		
		return $ref;
	}
	
	/**
	 * Copies all the content present in this object.
	 * @return static A new sequence with copied data.
	 */
	public function copy()
	{
		return clone $this;
	}
	
	/**
	 * Counts the number of elements currently in sequence.
	 * @return int Number of elements stored in the sequence.
	 */
	public function count(): int
	{
		return $this->data->count();
	}
	
	/**
	 * Return the element the internal pointer currently points to.
	 * @return mixed Current element in the sequence.
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
	 * @param int|int[] $keys Keys to be forgotten in the new sequence.
	 * @return static New sequence instance.
	 */
	public function except($keys)
	{
		return $this->new(
			array_diff_key($this->all(), array_flip(toArray($keys)))
		);
	}
	
	/**
	 * Filters elements according to the given test. If no test function is
	 * given, it fallbacks to removing all false equivalent values.
	 * @param callable $fn Test function. Parameters: value, key.
	 * @return static Sequence of all filtered values.
	 */
	public function filter(callable $fn = null)
	{
		return $this->new(is_null($fn)
			? array_filter($this->all())
			: array_filter($this->all(), $fn, ARRAY_FILTER_USE_BOTH)
		);
	}
	
	/**
	 * Returns the first element in the sequence.
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
			array_intersect($this->all(), toArray($items))
		);
	}
	
	/**
	 * Creates a generator that iterates over the sequence.
	 * @yield mixed Sequence's stored values.
	 */
	public function iterate(): \Generator
	{
		yield from $this->data;
	}
	
	/**
	 * Fetches the key the internal pointer currently points to.
	 * @return int Current element's key in the sequence.
	 */
	public function key(): int
	{
		return $this->data->key();
	}
	
	/**
	 * Returns the last element in the sequence.
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
	 * @param callable $fn Function to be used for creating new elements.
	 * @return static New sequence instance.
	 */
	public function map(callable $fn)
	{
		$target = $this->all();
		
		return $this->new(
			array_map($fn, $target, array_keys($target))
		);
	}
	
	/**
	 * Merges given items into sequence's elements.
	 * @param mixed $items Items to be merged into sequence.
	 * @return static Sequence of all merged elements.
	 */
	public function merge($items)
	{
		return $this->new(
			array_merge($this->all(), array_values($items))
		);
	}
	
	/**
	 * Advances the internal pointer one position.
	 * @return mixed Element in the next position.
	 */
	public function next()
	{
		$this->data->next();
		return $this->current();
	}
	
	/**
	 * Creates a new sequence with a subset of elements.
	 * @param int|int[] $keys Keys to be included in new sequence.
	 * @return static New sequence instance.
	 */
	public function only($keys)
	{
		return $this->new(
			array_intersect_key($this->all(), array_flip(toArray($keys)))
		);
	}
	
	/**
	 * Passes the sequence to the given function and returns the result.
	 * @param callable $fn Function to which sequence is passed to.
	 * @return mixed Callback's return result.
	 */
	public function pipe(callable $fn)
	{
		return $fn($this);
	}
	
	/**
	 * Pops an element out of the top of the sequence.
	 * @return mixed Popped element.
	 */
	public function pop()
	{
		return $this->data->pop();
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
	 * Pulls an element out of given index.
	 * @param int $index Index to be pulled off.
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
	 * Pushes an element onto the end of the sequence.
	 * @param mixed $value Value to be appended onto the sequence.
	 * @return $this Sequence for method chaining.
	 */
	public function push($value)
	{
		$this->data->push($value);
		return $this;
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
		
		$keys = array_rand($this->all(), $sample);
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
		return array_reduce($this->all(), $fn, $initial);
	}
	
	/**
	 * Returns a reversed copy of the sequence.
	 * @return static Reversed sequence.
	 */
	public function reverse()
	{
		return $this->new(array_reverse($this->all()));
	}
	
	/**
	 * Set the internal pointer of the sequence to its first element.
	 * @return mixed First element in sequence.
	 */
	public function rewind()
	{
		$this->data->rewind();
		return $this->data->current();
	}
	
	/**
	 * Rotates the sequence by a given number of rotations. If the number of
	 * rotations is positive, the elements in the bottom of the sequence will
	 * be rotated to the top. But, if the number of rotations is negative, the
	 * elements in the top of the sequence will be rotate to the bottom.
	 * @param int $rotations Number of rotations to be performed.
	 * @return static New rotate sequence.
	 */
	public function rotate(int $rotations = 1)
	{
		$btt = $rotations > 0;
		$rot = (abs($rotations) % $this->count()) * ($btt ? -1 : 1);
		$arr = $this->all();
		
		return $this->new(
			array_merge(array_slice($arr, $rot), array_slice($arr, 0, $rot))
		);
	}
	
	/**
	 * Attemps to find the index of an element stored in sequence.
	 * @param mixed $needle Value to be found.
	 * @param bool $strict Should the search be for identical elements?
	 * @return int|bool Index of the found element, or false otherwise.
	 */
	public function search($needle, bool $strict = false)
	{
		return array_search($needle, $this->all(), $strict);
	}
	
	/**
	 * Shifts a value off the bottom of the sequence.
	 * @return mixed Shifted value.
	 */
	public function shift()
	{
		return $this->data->shift();
	}
	
	/**
	 * Shuffles the sequence to an unknown order.
	 * @return static Shuffled sequence.
	 */
	public function shuffle()
	{
		$data = $this->all();
		shuffle($data);

		return $this->new($data);
	}
	
	/**
	 * Retrieves a slice of the sequence.
	 * @param int $index Slice initial position.
	 * @param int $length Length of the slice.
	 * @return static Sliced sequence.
	 */
	public function slice(int $index, int $length = null)
	{
		return $this->new(array_slice($this->all(), $index, $length));
	}
	
	/**
	 * Sorts the sequence using a given function.
	 * @param callable $fn Ordering function.
	 * @return static Sorted sequence.
	 */
	public function sort(callable $fn = null)
	{
		$data = $this->all();
		is_null($fn) ? sort($data) : usort($data, $fn);
		
		return $this->new($data);
	}
	
	/**
	 * Removes part of the sequence and replaces it.
	 * @param int $offset Initial splice offset.
	 * @param int $length Length of splice portion.
	 * @param mixed $replace Replacement for removed slice.
	 * @return static Spliced sequence.
	 */
	public function splice(int $offset, int $length = null, $replace = [])
	{
		return $this->new(array_splice(
			                  toArray($this->data),
			                  $offset,
			                  $length ?: $this->count(),
			                  $replace
		));
	}
	
	/**
	 * Splits the sequence into the given number of groups.
	 * @param int $count Number of groups to split the sequence.
	 * @return array Splitted sequence.
	 */
	public function split(int $count): array
	{
		return !$this->empty()
			? $this->chunk(ceil($this->count() / $count))
			: [];
	}
	
	/**
	 * Takes the first or last specified number of items.
	 * @param int $limit Number of items to be taken.
	 * @return static Sequence of the taken items.
	 */
	public function take(int $limit)
	{
		return $limit < 0
			? $this->slice($limit, $this->count())
			: $this->slice(0, $limit);
	}
	
	/**
	 * Passes the sequence to the given function and returns it.
	 * @param callable $fn Function to which sequence is passed to.
	 * @return static Sequence's copy sent to function.
	 */
	public function tap(callable $fn)
	{
		$fn($copy = $this->copy());
		return $copy;
	}
	
	/**
	 * Pushes an element onto the beginning of the sequence.
	 * @param mixed $value Value to be prepended onto the sequence.
	 * @return static Sequence for method chaining.
	 */
	public function unshift($value)
	{
		$this->data->unshift($value);
		return $this;
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
		$userdata = toArray($userdata);
		
		foreach($this->iterate() as $key => $value)
			$fn($value, $key, ...$userdata);
		
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
