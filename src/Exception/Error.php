<?php
/**
 * Zettacast\Exception\Error class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Exception;

/**
 * This class is responsible for carrying out an Error triggered by the PHP
 * internal engine in both framework's or application's code.
 * @package Zettacast\Exception
 * @version 1.0
 */
class Error extends \ErrorException
{
	/**
	 * Lists the names of each type of error.
	 * @var array Type error names.
	 */
	const NAME = [
		0                   => 'Exception',
		E_ERROR             => 'Error',
		E_WARNING           => 'Warning',
		E_PARSE             => 'Parse Error',
		E_NOTICE            => 'Notice',
		E_CORE_ERROR        => 'Core Error',
		E_CORE_WARNING      => 'Core Warning',
		E_COMPILE_ERROR     => 'Compile Error',
		E_COMPILE_WARNING   => 'Compile Warning',
		E_USER_ERROR        => 'User Error',
		E_USER_WARNING      => 'User Warning',
		E_USER_NOTICE       => 'User Notice',
		E_STRICT            => 'Strict Notice',
		E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
		E_DEPRECATED        => 'Deprecated',
		E_USER_DEPRECATED   => 'User Deprecated',
	];
	
	/**
	 * Informs the fatal error codes.
	 * @var int The fatal error severity codes.
	 */
	const FATAL = E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR;
	
	/**
	 * Error constructor. This constructor allows an Error instance to be
	 * created instead of an Exception one.
	 * @param string $msg The message related to the error.
	 * @param int $severity The severity code carried by the error.
	 * @param string $file The file where error occurred.
	 * @param int $line The line on file where error occurred.
	 */
	final public function __construct(
		string $msg = null,
		int $severity = null,
		string $file = null,
		int $line = null
	) {
		parent::__construct($msg, 0, $severity, $file, $line);
	}
	
	/**
	 * Informs whether the error being carried is fatal.
	 * @return bool Is captured error fatal?
	 */
	final public function fatal(): bool
	{
		return $this->severity & self::FATAL;
	}
	
	/**
	 * Builds a new framework Error instance from a built-in thrown instance.
	 * @param \Error $e The built-in Error instance to base instance on.
	 * @return static The framework Error instance.
	 */
	public static function build(\Error $e)
	{
		return new static(
			$e->getMessage(),
			$e instanceof \ParseError ? E_PARSE : E_ERROR,
			$e->getFile(),
			$e->getLine()
		);
	}
	
	/**
	 * Captures the last error internally thrown by PHP.
	 * @return static The framework Error instance based on last error.
	 */
	public static function last()
	{
		return !is_null($e = error_get_last())
			? new static($e['message'], $e['type'], $e['file'], $e['line'])
			: null;
	}
}

