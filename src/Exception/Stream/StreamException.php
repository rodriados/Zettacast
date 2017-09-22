<?php
/**
 * Zettacast\Exception\Stream\StreamException exception file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Exception\Stream;

class StreamException extends \Exception
{
	public static function couldNotBeOpened(string $uri)
	{
		return new static(sprintf(
			'The stream identified by "%s" could not be found or opened.',
			$uri
		));
	}
}
