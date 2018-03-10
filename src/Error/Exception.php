<?php
/**
 * Zettacast\Error\Exception class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Error;

class Exception extends \Exception
{
	/**
	 * Construct a new Exception instance. Although public, the inherited
	 * exceptions should all have static methods responsible for initializing
	 * the messages carried by them, instead of directly constructing them.
	 * @param string $msg The message carried by the exception.
	 * @param int $code Exception code to be carried.
	 * @param \Throwable $previous The previously thrown parent exception.
	 */
	final public function __construct (
		string $msg = null,
		int $code = null,
		\Throwable $previous = null
	) {
		parent::__construct($msg, $code, $previous);
	}
}
