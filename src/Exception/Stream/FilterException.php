<?php
/**
 * Zettacast\Exception\Stream\FilterException exception file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Exception\Stream;

class FilterException extends StreamException
{
	public static function isNotKnown(string $filtername)
	{
		return new static(sprintf(
			'The filter identified by "%s" is not known.',
			$filtername
		));
	}
}
