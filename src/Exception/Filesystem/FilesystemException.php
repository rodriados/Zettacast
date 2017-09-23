<?php
/**
 * Zettacast\Exception\Filesystem\FilesystemException exception file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Exception\Filesystem;

use Zettacast\Exception\Exception;

class FilesystemException extends Exception
{
	public static function missingDirectory(string $dirname)
	{
		return new static(sprintf(
			'The directory "%s" is missing or could not be accessed.',
			$dirname
		));
	}
	
	public static function missingFile(string $filename, Exception $e = null)
	{
		return new static(sprintf(
			'The file "%s" does not exist or could not be accessed.',
			$filename
		), null, $e);
	}
	
}
