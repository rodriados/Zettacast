<?php
/**
 * Zettacast\Stream\Filter\CallableFilter class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Stream\Filter;

use Zettacast\Stream\AbstractFilter;

/**
 * This stream filter delegates its processing to a callable variable. The
 * callable will receive the string being filtered and must return it's
 * filtered counterpart.
 * @package Zettacast\Stream\Filter
 * @version 1.0
 */
class CallableFilter extends AbstractFilter
{
	/**
	 * The callable instance responsible for filtering stream.
	 * @var callable The callable value responsible for filtering.
	 */
	protected $callable;
	
	/**
	 * CallableFilter constructor.
	 * The constructor simply sets what callable should use to filter stream,
	 * and must not be directly called.
	 * @param string $filtername Name used for filter instantiation.
	 * @param callable $fn The callable value.
	 */
	protected function __construct(string $filtername, callable $fn)
	{
		$this->callable = $fn;
	}
	
	/**
	 * This method delegates its processing to given callable. Thus, any
	 * changes to data are solely callable's responsibily.
	 * @param bool $closing Informs whether filter is closing or not.
	 * @return int Filtering status value.
	 */
	public function process(bool $closing): int
	{
		if($content = $this->read())
			$this->write(($this->callable)($content));
		
		return self::SUCCESS;
	}
}
