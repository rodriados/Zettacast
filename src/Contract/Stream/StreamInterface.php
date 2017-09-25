<?php
/**
 * Zettacast\Contract\Stream\StreamInterface interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Contract\Stream;

use Zettacast\Contract\ExtractableInterface;

/**
 * The Stream interface is responsible for exposing mandatory methods a stream
 * handler must have.
 * @package Zettacast\Stream
 */
interface StreamInterface extends ExtractableInterface
{
	/**#@+
	 * Stream channel identifiers used for filtering.
	 * @var int Identification for stream channels.
	 */
	const WRITE = STREAM_FILTER_WRITE;
	const READ = STREAM_FILTER_READ;
	const ALL = STREAM_FILTER_ALL;
	/**#@-*/
	
	/**
	 * Checks for the end-of-file pointer.
	 * @return bool Has end-of-file been reached?
	 */
	public function eof(): bool;
	
	/**
	 * Binds a filter to the stream's input or output channels.
	 * @param string|FilterInterface $filter Filter to be binded to stream.
	 * @param bool $prepend Should this filter be executed before all others?
	 * @param int $channel Channel to be filtered.
	 * @return FilterInterface Filter instance.
	 */
	public function filter(
		$filter,
		int $channel = self::ALL,
		bool $prepend = false
	): FilterInterface;
	
	/**
	 * Sets the stream pointer to the end of stream.
	 * @return bool Was the operation successful?
	 */
	public function forward(): bool;
	
	/**
	 * Retrieves some metadata about the stream.
	 * @param string $data Data name to be retrieved.
	 * @return mixed The metadata value.
	 */
	public function metadata(string $data);
	
	/**
	 * Informs the mode this stream has been opened with.
	 * @return string The stream access mode.
	 */
	public function mode(): string;
	
	/**
	 * Offsets the stream pointer by the given amount.
	 * @param int $offset Number of bytes to be offset.
	 * @return bool Was the operation successful?
	 */
	public function offset(int $offset): bool;
	
	/**
	 * Retrieves contents from stream.
	 * @param int $length Maximum number of bytes to be read from stream.
	 * @return string Read stream contents.
	 */
	public function read(int $length = null): string;
	
	/**
	 * Retrieves contents from stream and puts it into another stream.
	 * @param resource|StreamInterface $target Target to put content on.
	 * @param int $length Maximum number of bytes to be retrieved from stream.
	 * @return int Length of data read out of stream.
	 */
	public function readTo($target, int $length = null): int;
	
	/**
	 * Sets the stream pointer to the beginning of stream.
	 * @return bool Was the operation successful?
	 */
	public function rewind(): bool;
	
	/**
	 * Seeks the stream pointer.
	 * @param int $offset The position to be seeked.
	 * @return bool Was the operation successful?
	 */
	public function seek(int $offset): bool;
	
	/**
	 * Tells the current stream pointer position.
	 * @return int The current stream pointer offset.
	 */
	public function tell(): int;
	
	/**
	 * Informs the locator used for instantiating this stream.
	 * @return string The locator of this stream.
	 */
	public function uri(): string;
	
	/**
	 * Writes contents to stream.
	 * @param string $content Content to be written to stream.
	 * @param int $length Maximum length of data to be written to stream.
	 * @return int Length of data written to stream.
	 */
	public function write(string $content, int $length = null): int;
	
	/**
	 * Retrieves content from stream and writes it to another stream.
	 * @param resource|StreamInterface $source Source to get content from.
	 * @param int $length Maximum number of bytes to be written to stream.
	 * @return int Total length of data written to stream.
	 */
	public function writeFrom($source, int $length = null): int;
	
}
