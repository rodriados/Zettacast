<?php
/**
 * Zettacast\Contract\Filesystem\Stream interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Contract\Filesystem;

/**
 * The Stream interface is responsible for exposing mandatory methods a
 * FileSystem file handler must have.
 * @package Zettacast\Contract\Filesystem
 */
interface Stream
{
	/**
	 * Retrieves contents from stream.
	 * @param int $length Maximum number of bytes to be read from stream.
	 * @return string Read stream contents.
	 */
	public function read(int $length = null) : string;
	
	/**
	 * Retrieves contents from stream and puts it into another stream.
	 * @param resource|Stream $target Target to put content on.
	 * @param int $length Maximum number of bytes to be retrieved from stream.
	 * @return int Length of data read out of stream.
	 */
	public function readTo($target, int $length = null) : int;
	
	/**
	 * Writes contents to stream.
	 * @param string $content Content to be written to stream.
	 * @param int $length Maximum length of data to be written to stream.
	 * @return int Length of data written to stream.
	 */
	public function write(string $content, int $length = null) : int;
	
	/**
	 * Retrieves content from stream and writes it to another stream.
	 * @param resource|Stream $source Source content is retrieved from.
	 * @param int $length Maximum number of bytes to be written to stream.
	 * @return int Total length of data written to stream.
	 */
	public function writeFrom($source, int $length = null) : int;
	
}
