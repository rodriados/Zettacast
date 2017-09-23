<?php
/**
 * Zettacast\Exception\Handler abstract class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Exception;

/**
 * This abstract class is responsible for holding all error and exception
 * handlers used by the framework. If needed, a Dumper instance must be invoked
 * to show and log the error.
 * @package Zettacast\Exception
 * @version 1.0
 */
abstract class Handler
{
	/**
	 * Handles internal PHP triggered errors.
	 * @param int $level The error severity.
	 * @param string $message The message carried by the error.
	 * @param string $file The file where error occurred.
	 * @param int $line The line on file where error occurred.
	 * @throws Error A framework error instance.
	 */
	public static function handleError(
		int $level,
		string $message,
		string $file = null,
		int $line = null
	) {
		if(error_reporting() & $level)
			throw new Error($message, $level, $file, $line);
	}
	
	/**
	 * Handles uncaught exceptions and fatal errors, logs and shows them if
	 * needed or allowed.
	 * @param \Throwable $e The exception or error to be handled.
	 * @return bool Was the exception successfully handled?
	 */
	public static function handleException(\Throwable $e)
	{
		if($e instanceof \Error)
			$e = Error::build($e);
		
		if($e instanceof Error) {
			print sprintf("%s!\n%s in %s on line %d.\n\n",
				Error::NAME[$e->getSeverity()], $e->getMessage(),
				$e->getFile(), $e->getLine()
			);
			
			print $e->getTraceAsString();
			return true;
		}
		
		print sprintf("Uncaught Exception: %s!\n", get_class($e));
		print sprintf('%s in %s on line %d.', $e->getMessage(), $e->getFile(), $e->getLine());
		print "\n\n";
		print $e->getTraceAsString();
		return true;
	}
	
	/**
	 * Handles the script shutdown. This method verifies whether the execution
	 * was interrupted by a fatal error and proceeds to log it.
	 */
	public static function handleShutdown()
	{
		if(!is_null($e = Error::last()) && $e->isFatal())
			self::handleException($e);
	}
}
