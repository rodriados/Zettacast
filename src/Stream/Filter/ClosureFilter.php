<?php
/**
 * Zettacast\Stream\Filter\ClosureFilter class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Stream\Filter;

use Zettacast\Contract\Stream\AbstractFilter;

/**
 * This stream filter delegates its processing to a closure. The closure can
 * freely use this object's public and protected methods and properties.
 * @package Zettacast\Stream
 * @version 1.0
 */
class ClosureFilter extends AbstractFilter
{
	/**
	 * The closure responsible for processing the filter.
	 * @var \Closure Filter function.
	 */
	protected $closure;
	
	/**
	 * ClosureFilter constructor. The constructor prepares the function to be
	 * able to use the object's methods and properties via `$this`.
	 * @param string $filtername Name used for filter instantiation.
	 * @param \Closure $params Filter function.
	 */
	public function __construct(string $filtername, \Closure $params)
	{
		$this->closure = $params;
		$this->closure->bindTo($this, static::class);
	}
	
	/**
	 * This method delegades its processing to the given closure. Thus, any
	 * changes to the data are the closure's responsibily.
	 * @param bool $closing Informs whether the filter is closing or not.
	 * @return int Filtering status value.
	 */
	public function process(bool $closing): int
	{
		return $this->closure->call($this, $closing);
	}
	
}
