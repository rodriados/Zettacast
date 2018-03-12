<?php
/**
 * Zettacast\Stream\Filter\ClosureFilter class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Stream\Filter;

use Zettacast\Stream\AbstractFilter;

/**
 * This stream filter delegates its processing to a closure. The closure can
 * freely use this object's public and protected methods and properties.
 * @package Zettacast\Stream\Filter
 * @version 1.0
 */
class ClosureFilter extends AbstractFilter
{
	/**
	 * The closure responsible for filtering stream.
	 * @var \Closure Filter function responsible for filtering stream.
	 */
	protected $closure;
	
	/**
	 * ClosureFilter constructor.
	 * The constructor prepares the function to be able to use the object's
	 * methods and properties via `$this`, and must not be directly called.
	 * @param string $filtername Name used for filter instantiation.
	 * @param \Closure $fn Filter function.
	 */
	protected function __construct(string $filtername, \Closure $fn)
	{
		$this->closure = $fn;
		$this->closure->bindTo($this, static::class);
	}
	
	/**
	 * This method delegates its processing to given closure. Thus, any changes
	 * to data are solely closure's responsibily.
	 * @param bool $closing Informs whether filter is closing or not.
	 * @return int Filtering status value.
	 */
	public function process(bool $closing): int
	{
		return $this->closure->call($this, $closing);
	}
}
