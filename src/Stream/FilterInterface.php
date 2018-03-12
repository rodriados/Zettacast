<?php
/**
 * Zettacast\Stream\FilterInterface interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Stream;

/**
 * The stream filter interface is responsible for exposing mandatory methods a
 * stream filter instance must have. An implementation of this abstraction will
 * be held responsible of managing filter instances.
 * @package Zettacast\Stream
 */
interface FilterInterface
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
	 * Appends filter to a stream.
	 * @param resource $stream Stream to filter.
	 * @param int $channel Stream channel to filter.
	 */
	public function append($stream, int $channel = self::ALL);
	
	/**
	 * Prepends filter to a stream.
	 * @param resource $stream Stream to filter.
	 * @param int $channel Stream channel to filter.
	 */
	public function prepend($stream, int $channel = self::ALL);
	
	/**
	 * Removes filter from all streams it was applied to.
	 */
	public function remove();
}
