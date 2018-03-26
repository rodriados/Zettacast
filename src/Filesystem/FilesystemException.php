<?php
/**
 * Zettacast\Filesystem\FilesystemException exception file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Filesystem;

use Zettacast\Exception\Exception;

/**
 * This class informs an error occurred during a filesystem operation.
 * @package Zettacast\Filesystem
 * @version 1.0
 */
class FilesystemException extends Exception
{
	/**
	 * Creates a filesystem operation exception.
	 * @param string $dirname The name of missing directory.
	 * @return static The created filesystem exception.
	 */
	public static function missingdir(string $dirname)
	{
		return static::format(
			_('The directory "%1$s" is missing or could not be accessed.'),
			$dirname
		);
	}
	
	/**
	 * Creates a filesystem operation exception.
	 * @param string $filename The name of missing file.
	 * @param Exception $e The parent exception previously raised.
	 * @return static The created filesystem exception.
	 */
	public static function missingfile(string $filename, Exception $e = null)
	{
		return new static(sprintf(
			_('The file "%1$s" does not exist or could not be accessed.'),
			$filename
		), null, $e);
	}
}
