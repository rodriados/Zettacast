<?php
/**
 * Zettacast\FileSystem\Contract\Handler interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\FileSystem\Contract;

/**
 * The Handler interface is responsible for exposing mandatory methods a
 * FileSystem file handler must have.
 * @package Zettacast\FileSystem\Contract
 */
interface Handler
{
	/**
	 * Retrieves contents from handled file.
	 * @param int $length Maximum number of bytes to be read from file.
	 * @return string All file contents.
	 */
	public function read(int $length = null) : string;
	
	/**
	 * Retrieves contents from file and puts it to a stream or another file.
	 * @param resource|Handler $target Target to which content is put on.
	 * @param int $length Maximum number of bytes to be retrieved from file.
	 */
	public function readTo($target, int $length = null);
	
	/**
	 * Writes contents to handled file.
	 * @param string $content Content to be written to file.
	 * @param int $length Maximum length of data to be written to file.
	 * @return int Length of data written to file.
	 */
	public function write(string $content, int $length = null) : int;
	
	/**
	 * Retrieves content from stream or file and writes it to the handled file.
	 * @param resource|Handler $source Source content is retrieved from.
	 * @param int $length Maximum number of bytes to be written to file.
	 * @return int Length of data written to file.
	 */
	public function writeFrom($source, int $length = null) : int;

}
