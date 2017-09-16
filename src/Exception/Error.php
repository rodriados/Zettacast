<?php
/**
 * Zettacast\Exception\Error class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Exception;

use Throwable;
use ErrorException;

class Error
	extends ErrorException
{
	private static $properties = [
		'code'     => 'getCode',
		'file'     => 'getFile',
		'line'     => 'getLine',
		'trace'    => 'getTrace',
		'message'  => 'getMessage',
		'severity' => 'getSeverity',
	];
	
	final public function __construct(
		string $msg = null,
		int $code = null,
		int $severity = null,
		string $file = null,
		int $line = null,
		Throwable $previous = null
	) {
		parent::__construct($msg, $code, $severity, $file, $line, $previous);
	}
	
	public function __get(string $name)
	{
		return array_key_exists($name, self::$properties)
			? $this->{self::$properties[$name]}()
			: null;
	}
	
	public function fatal() : bool
	{
		return $this->code & (E_ERROR|E_PARSE|E_CORE_ERROR|E_COMPILE_ERROR);
	}
	
	public static function last()
	{
		return static::build(error_get_last());
	}
	
	protected static function build(array $e = null)
	{
		$keys = array_keys($e);
		sort($keys);
		
		return !is_null($e) && $keys == ['file', 'line', 'message', 'type']
			? new static($e['message'], $e['type'], 0, $e['file'], $e['line'])
			: null;
	}
	
}

