<?php
/**
 * Zettacast\Uri\UriException class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */

namespace Zettacast\Uri;

use Zettacast\Exception\Exception;

class UriException extends Exception
{
	/**
	 * Creates a invalid URI stream exception.
	 * @param string $uri The invalid URI.
	 * @return static The created stream exception.
	 */
	public static function invalid(string $uri)
	{
		return static::format(
			_('The given URI "%1$s" is invalid.'),
			$uri
		);
	}
	
	/**
	 * Creates a invalid URI stream exception.
	 * @param string $part Name of invalid component.
	 * @param string $data Data sent for component.
	 * @return static The created stream exception.
	 */
	public static function unmatched(string $part, string $data)
	{
		return static::format(
			_('The value "%2$s" is an invalid URI %1$s.'),
			$part, $data
		);
	}
}
