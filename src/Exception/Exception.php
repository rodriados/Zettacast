<?php
/**
 * Zettacast\Error\Exception class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Exception;

/**
 * This class is the base for all exceptions thrown by the framework. All of
 * the application exceptions should inherit from this one, but that cannot be
 * enforced by the framework.
 * @package Zettacast\Exception
 * @version 1.0
 */
class Exception extends \Exception
{
	/**
	 * Exception constructor.
	 * Constructs a new exception. Although public, the inherited exceptions
	 * should all have static methods responsible for initializing the messages
	 * carried by them, instead of directly constructing them.
	 * @param string $msg The message carried by the exception.
	 * @param int $code Exception code to be carried.
	 * @param \Throwable $previous The previously thrown parent exception.
	 */
	final public function __construct(
		string $msg,
		int $code = null,
		\Throwable $previous = null
	) {
		parent::__construct($msg, $code, $previous);
	}
}

