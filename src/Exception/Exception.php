<?php
/**
 * Zettacast\Error\Exception class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Exception;

use Throwable;
use Exception as BuiltinException;

class Exception
	extends BuiltinException
{
	private static $properties = [
		'code'    => 'getCode',
		'file'    => 'getFile',
		'line'    => 'getLine',
		'trace'   => 'getTrace',
		'message' => 'getMessage',
	];
	
	final public function __construct(
		string $msg = null,
		int $code = null,
		Throwable $previous = null
	) {
		parent::__construct($msg, $code, $previous);
	}
	
	public function __get(string $name)
	{
		return array_key_exists($name, self::$properties)
			? $this->{self::$properties[$name]}()
			: null;
	}
	
}

