<?php
/**
 * Zettacast\Support\UrlException class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Support;

use Zettacast\Exception\Exception;

class UriException extends Exception
{
	/**
	 * Creates an invalid URL exception.
	 * @param string $url The invalid URL.
	 * @return static The created exception.
	 */
	public static function invalid(string $url)
	{
		return new static(sprintf(
			_('The URL "%s" is invalid!'),
			$url
		));
	}
}
