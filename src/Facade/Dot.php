<?php
/**
 * Dot façade file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Facade;

use Zettacast\Helper\Extensor;
use Zettacast\Collection\Dot as baseclass;

/**
 * Zettacast's Dot façade class.
 * This class exposes package:collection\Dot methods to external usage.
 * @method static baseclass filter($target, callable $fn = null)
 * @method static baseclass get($target, $key, $default = null)
 * @method static bool has($target, $key)
 * @method static baseclass pluck($target, $value, $key = null)
 * @method static baseclass set($target, $key, $value)
 * @method static baseclass remove($target, $key)
 * @method static baseclass apply($target, callable $fn, ...$userdata)
 * @method static baseclass collapse($target)
 * @method static baseclass flatten($target)
 * @method static baseclass iterate($target, bool $listall = false)
 * @method static baseclass map($target, callable $fn)
 * @method static baseclass reduce($target, callable $fn, $initial = null)
 * @method static baseclass walk($target, callable $fn, ...$userdata)
 * @method static baseclass add($target, $key, $value)
 * @method static array all($target)
 * @method static array chunk($target, int $size)
 * @method static array clear($target)
 * @method static baseclass copy($target)
 * @method static int count($target)
 * @method static baseclass current($target)
 * @method static baseclass diff($target, $items, bool $keys = false)
 * @method static array divide($target)
 * @method static bool empty($target)
 * @method static bool every($target, callable $fn = null)
 * @method static baseclass except($target, ...$keys)
 * @method static baseclass intersect($target, $items, bool $keys = false)
 * @method static baseclass key($target)
 * @method static Collection keys($target)
 * @method static baseclass merge($target, $items)
 * @method static baseclass next($target)
 * @method static baseclass only($target, ...$keys)
 * @method static baseclass pipe($target, callable $fn)
 * @method static baseclass prev($target)
 * @method static baseclass pull($target, $key, $default = null)
 * @method static baseclass random($target, int $sample = 1)
 * @method static baseclass replace($target, $items)
 * @method static baseclass rewind($target)
 * @method static baseclass search($target, $needle, bool $strict = false)
 * @method static baseclass shuffle($target)
 * @method static array split($target, int $count)
 * @method static baseclass tap($target, callable $fn)
 * @method static baseclass union($target, $items)
 * @method static baseclass unique($target)
 * @method static bool valid($target)
 * @method static Collection values($target)
 * @method static array zip($target, ...$items)
 * @version 1.0
 */
final class Dot
{
	use Extensor
	{
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
		if(count($args) >= 1 && is_callable([baseclass::class, $method]))
			return self::build(array_shift($args))->$method(...$args);
		
		return self::callExtensor($method, $args);
	}
	
	/**
	 * Builds a new instance of the façaded object.
	 * @param array|\Traversable $target Instance initial values.
	 * @return baseclass New created instance.
	 */
	public static function build($target = [])
	{
		return new baseclass($target);
	}
	
}
