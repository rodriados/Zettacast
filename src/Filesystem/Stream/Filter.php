<?php
/**
 * Zettacast\Filesystem\Stream\Filter class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Filesystem\Stream;

/**
 * This class manages filters to be applied to streams.
 * @package Zettacast\Filesystem\Stream
 * @version 1.0
 */
class Filter
{
	/**
	 * Parameter to be passed to filter object.
	 * @var mixed Filter parameter.
	 */
	protected $param;
	
	/**
	 * The name of the filter to be applied to stream.
	 * @var string Filter name.
	 */
	protected $filtername;
	
	/**
	 * All filter instances tracked by this object.
	 * @var resource[] Filter instances.
	 */
	private $instances;
	
	/**
	 * Filter constructor.
	 * @param string $filtername Filter name to applied to stream.
	 * @param mixed $param Parameter to be passed to filter.
	 */
	public function __construct(string $filtername, $param = null)
	{
		$this->param = $param;
		$this->filtername = $filtername;
		$this->instances = [];
	}
	
	/**
	 * Appends filter to a stream.
	 * @param resource $stream Stream to be filtered.
	 * @param int $channel Stream channel to be filtered.
	 * @return static Filter for method chaining.
	 */
	public function append($stream, int $channel = Stream::ALL)
	{
		$this->instances[] = stream_filter_append(
			$stream,
			$this->filtername,
			$channel,
			$this->param
		);
		
		return $this;
	}
	
	/**
	 * Prepends filter to a stream.
	 * @param resource $stream Stream to be filtered.
	 * @param int $channel Stream channel to be filtered.
	 * @return static Filter for method chaining.
	 */
	public function prepend($stream, int $channel = Stream::ALL)
	{
		$this->instances[] = stream_filter_prepend(
			$stream,
			$this->filtername,
			$channel,
			$this->param
		);
		
		return $this;
	}
	
	/**
	 * Removes all filter instances tracked by this object.
	 * @return static Filter for method chaining.
	 */
	public function remove()
	{
		foreach($this->instances as  $filter)
			stream_filter_remove($filter);
		
		return $this;
	}
	
	/**
	 * Retrieves a list of all currently available filters.
	 * @return array List of registered filters.
	 */
	public static function list() : array
	{
		return stream_get_filters();
	}
	
	/**
	 * Registers a class as a stream filter, making it available for use.
	 * @param string $filter Filter name to be registered.
	 * @param string $class Class responsible for responding to filter calls.
	 * @return bool Was the filter successfully registered?
	 */
	public static function register(string $filter, string $class) : bool
	{
		return stream_filter_register($filter, $class);
	}
	
}
