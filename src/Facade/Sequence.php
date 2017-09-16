<?php
/**
 * Sequence façade file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Facade;

use Zettacast\Helper\Extensor;
use Zettacast\Collection\Sequence as baseclass;

/**
 * Zettacast's Sequence façade class.
 * This class exposes package:collection\Sequence methods to external usage.
 * @method static mixed get($target, int $index, $default = null)
 * @method static bool has($target, int $index)
 * @method static baseclass set($target, int $index, $value)
 * @method static baseclass del($target, int $index)
 * @method static array all($target)
 * @method static baseclass apply($target, callable $fn, $userdata = null)
 * @method static array chunk($target, int $size)
 * @method static array clear($target)
 * @method static baseclass copy($target)
 * @method static int count($target)
 * @method static baseclass current($target)
 * @method static bool empty($target)
 * @method static bool every($target, callable $fn = null)
 * @method static baseclass except($target, int|int[] $keys)
 * @method static baseclass filter($target, callable $fn = null)
 * @method static baseclass first($target)
 * @method static baseclass intersect($target, $items)
 * @method static baseclass iterate($target)
 * @method static baseclass key($target)
 * @method static baseclass last($target)
 * @method static baseclass map($target, callable $fn)
 * @method static baseclass merge($target, $items)
 * @method static baseclass next($target)
 * @method static baseclass only($target, int|int[] $keys)
 * @method static baseclass pipe($target, callable $fn)
 * @method static baseclass pop($target)
 * @method static baseclass prev($target)
 * @method static baseclass pull($target, int $index, $default = null)
 * @method static baseclass push($target, $value)
 * @method static baseclass random($target, int $sample = 1)
 * @method static baseclass reduce($target, callable $fn, $initial = null)
 * @method static baseclass reverse($target)
 * @method static baseclass rewind($target)
 * @method static baseclass rotate($target, int $rotations = 1)
 * @method static baseclass search($target, $needle, bool $strict = false)
 * @method static baseclass shift($target)
 * @method static baseclass shuffle($target)
 * @method static baseclass slice($target, int $index, int $length = null)
 * @method static baseclass sort($target, callable $fn = null)
 * @method static baseclass splice($target, int $offset, int $length = null, $replace = null)
 * @method static array split($target, int $count)
 * @method static baseclass take($target, int $limit)
 * @method static baseclass tap($target, callable $fn)
 * @method static baseclass unshift($target, $value)
 * @method static bool valid($target)
 * @method static baseclass walk($target, callable $fn, $userdata = null)
 * @version 1.0
 */
final class Sequence
{
	use Extensor {
		Extensor::__callStatic as private callExtensor;
	}
	
	/**
	 * Handles dynamic static calls to the façaded object.
	 * @param string $method Method to be called.
	 * @param array $args Arguments for the called method.
	 * @return mixed Façaded method return value.
	 */
	public static function __callStatic(string $method, array $args)
	{
		return count($args) >= 1 && is_callable([baseclass::class, $method])
			? self::build(array_shift($args))->$method(...$args)
			: self::callExtensor($method, $args);
	}
	
	/**
	 * Builds a new instance of the façaded object.
	 * @param array|\Traversable $target Instance initial values.
	 * @return baseclass New created instance.
	 */
	public static function build($target = []): baseclass
	{
		return new baseclass($target);
	}
	
	/**
	 * Creates a new sequence and fills it with the given value.
	 * @param int $count Number of elements to be filled.
	 * @param mixed $value Value to fill collection with.
	 * @return baseclass New sequence instance.
	 */
	public static function fill(int $count, $value): baseclass
	{
		return self::build(array_fill(0, $count, $value));
	}
	
	/**
	 * Creates a new sequence based on a range of number or letters.
	 * @param mixed $start Starting value of the range.
	 * @param mixed $end Limiting value of the range.
	 * @param int $step Increment step for each element in range.
	 * @return baseclass New sequence instance.
	 */
	public static function range($start, $end, int $step = 1): baseclass
	{
		return self::build(range($start, $end, $step));
	}
	
}
