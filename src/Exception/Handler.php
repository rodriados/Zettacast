<?php
/**
 * Zettacast\Exception\Handler class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Exception;

class Handler
{
	public static function error(
		int $level,
		string $message,
		string $file = null,
		int $line = null
	) {
		if(error_reporting() & $level)
			throw new Error($message, 0, $level, $file, $line);
	}
	
	public static function exception($args)
	{
		# @todo Implement the Exception handler
	}
	
	public static function shutdown()
	{
		if(!is_null($err = Error::last()) && $err->fatal())
			self::exception($err);
	}
}
