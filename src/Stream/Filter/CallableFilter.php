<?php
/**
 * Zettacast\Stream\Filter\CallableFilter class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Stream\Filter;

use Zettacast\Contract\Stream\AbstractFilter;

/**
 * This stream filter delegates its processing to a callable variable.
 * The callable will receive the string being filtered and must return it's
 * filtered counterpart.
 * @package Zettacast\Stream
 * @version 1.0
 */
class CallableFilter extends AbstractFilter
{
	/**
	 * The callable instance responsible for processing the filter.
	 * @var callable The callable value responsible for filtering.
	 */
	protected $callable;
	
	/**
	 * CallableFilter constructor. The constructor simply sets what callable
	 * should be used to filter the stream.
	 * @param string $filtername Name used for filter instantiation.
	 * @param callable $params The callable value.
	 */
	public function __construct(string $filtername, callable $params)
	{
		$this->callable = $params;
	}
	
	/**
	 * This method delegades its processing to the given closure. Thus, any
	 * changes to the data are the closure's responsibily.
	 * @param bool $closing Informs whether the filter is closing or not.
	 * @return int Filtering status value.
	 */
	public function process(bool $closing): int
	{
		if($content = $this->read())
			$this->write(($this->callable)($content));
		
		return self::SUCCESS;
	}
	
}
