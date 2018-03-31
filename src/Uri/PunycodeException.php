<?php
/**
 * Zettacast\Uri\PunycodeException class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */

namespace Zettacast\Uri;

use Zettacast\Exception\Exception;

class PunycodeException extends Exception
{
	/**
	 * Creates a punycode exception.
	 * @return static The created punycode exception.
	 */
	public static function invalid()
	{
		return static::format(
			_('Input is invalid')
		);
	}
	
	/**
	 * Creates a punycode exception.
	 * @return static The created punycode exception.
	 */
	public static function notbasic()
	{
		return static::format(
			_('Input has an illegal character.')
		);
	}
	
	/**
	 * Creates a punycode exception.
	 * @return static The created punycode exception.
	 */
	public static function overflow()
	{
		return static::format(
			_('Input needs wider integers to process.')
		);
	}
}
