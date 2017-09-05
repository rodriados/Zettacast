<?php
/**
 * Zettacast\Filesystem\File class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Filesystem;

use Zettacast\Filesystem\Stream\Stream;
use Zettacast\Filesystem\Stream\Virtual;
use Zettacast\Filesystem\Exception\FileDoesNotExist;
use Zettacast\Filesystem\Exception\StreamDoesNotExist;

/**
 * This class acts as wrapper to a local stream as a file handler.
 * @package Zettacast\Filesystem
 * @version 1.0
 */
class File
	extends Stream
{
	/**
	 * File constructor.
	 * @param string $filename File to be opened.
	 * @param string $mode Access mode for opening file.
	 * @throws FileDoesNotExist File could not be found.
	 */
	public function __construct(string $filename, string $mode = 'r')
	{
		try {
			parent::__construct('file://'.$filename, $mode);
		} catch(StreamDoesNotExist $e) {
			throw new FileDoesNotExist($filename);
		}
	}
	
	/**
	 * Creates a new temporary file, to be removed at this object destruction.
	 * @return Virtual New temporary file.
	 */
	public static function virtual()
	{
		return new Virtual;
	}
	
}
