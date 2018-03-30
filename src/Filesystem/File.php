<?php
/**
 * Zettacast\Filesystem\File class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Filesystem;

use Zettacast\Stream\Stream;
use Zettacast\Stream\StreamException;

/**
 * This class acts as wrapper to a local stream as a file handler.
 * @package Zettacast\Filesystem
 * @version 1.0
 */
class File extends Stream
{
	/**
	 * File constructor.
	 * Opens file and sets it up for usage.
	 * @param mixed $filename File to open.
	 * @param string $mode Access mode for opening file.
	 * @throws FilesystemException File could not be found.
	 */
	public function __construct($filename, string $mode = 'rb')
	{
		try {
			parent::__construct($filename, $mode);
		}
		
		catch(StreamException $e) {
			throw FilesystemException::missingfile($filename, $e);
		}
	}
}
