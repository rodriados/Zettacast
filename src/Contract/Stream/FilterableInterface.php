<?php
/**
 * Zettacast\Contract\Stream\FilterableInterface interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Contract\Stream;

/**
 * The Filterable stream interface is responsible for exposing mandatory
 * methods a Stream that can be filtered must have.
 * @package Zettacast\Stream
 */
interface FilterableInterface extends StreamInterface
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
	
}
