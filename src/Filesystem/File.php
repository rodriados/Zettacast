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
use Zettacast\Exception\Stream\StreamException;
use Zettacast\Exception\Filesystem\FilesystemException;

/**
 * This class acts as wrapper to a local stream as a file handler.
 * @package Zettacast\Filesystem
 * @version 1.0
 */
class File extends Stream
{
	/**
	 * File constructor.
	 * @param string $filename File to be opened.
	 * @param string $mode Access mode for opening file.
	 * @throws FilesystemException File could not be found.
	 */
	public function __construct(string $filename, string $mode = 'r')
	{
		try {
			parent::__construct('file://'.$filename, $mode);
		} catch(StreamException $e) {
			throw FilesystemException::missingFile($filename, $e);
		}
	}
	
}
