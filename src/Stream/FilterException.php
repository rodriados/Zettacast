<?php
/**
 * Zettacast\Stream\FilterException exception file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Stream;

/**
 * This class informs an error occurred during execution of a stream filter.
 * @package Zettacast\Stream
 * @version 1.0
 */
class FilterException extends StreamException
{
	/**
	 * Creates a stream filter exception.
	 * @param string $filtername Filter in which error occurred.
	 * @return static The created stream filter exception.
	 */
	public static function unknown(string $filtername)
	{
		return new static(sprintf(
			_('The filter identified by "%s" is not known.'),
			$filtername
		));
	}
}
