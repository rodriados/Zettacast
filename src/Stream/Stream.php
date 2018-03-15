<?php
/**
 * Zettacast\Stream\Stream class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Stream;

/**
 * This class handles all interactions to a stream.
 * @package Zettacast\Filesystem\Stream
 * @version 1.0
 */
class Stream implements StreamInterface
{
	/**
	 * Internal stream handler, responsible for keeping it open for
	 * access and intermediating all operations.
	 * @var resource Internal file handler.
	 */
	protected $handler;
	
	/**
	 * Stream constructor.
	 * This constructor opens stream from given locator.
	 * @param string $url Locator of stream to open.
	 * @param string $mode Opening mode for stream access.
	 * @param array|StreamContext The context related to stream.
	 * @throws StreamException Stream could not be found.
	 */
	public function __construct(string $url, string $mode = 'r', $context = [])
	{
		if(!empty($context))
			$context = !$context instanceof StreamContext
				? StreamContext::create($context)
				: $context->raw();
		
		$handler = !empty($context)
			? @fopen($url, $mode, false, $context)
			: @fopen($url, $mode);
		
		if(!$this->handler = $handler)
			throw StreamException::unopened($url);
	}
	
	/**
	 * Stream destructor.
	 * Destructs stream handler before this object's destruction, thus
	 * preserving all changes eventually made to stream's contents.
	 */
	public function __destruct()
	{
		fclose($this->handler);
	}
	
	/**
	 * Checks for end-of-file pointer.
	 * @return bool Has end-of-file been reached?
	 */
	public function eof(): bool
	{
		return feof($this->handler);
	}
	
	/**
	 * Binds a filter to stream's input or output channels.
	 * @param FilterInterface|mixed $filter Filter to apply to stream.
	 * @param bool $prepend Should this filter execute before all others?
	 * @param int $channel Channel to filter.
	 * @return FilterInterface Filter instance.
	 */
	public function filter(
		$filter,
		int $channel = self::ALL,
		bool $prepend = false
	): FilterInterface {
		$method = $prepend
			? 'prepend'
			: 'append';
		
		return $filter instanceof FilterInterface
			? $filter->$method($this->handler, $channel)
			: with(new Filter($filter))->$method($this->handler, $channel);
	}
	
	/**
	 * Forces a write of all buffered output to stream.
	 * @return bool Was buffer successfully flushed?
	 */
	public function flush(): bool
	{
		return fflush($this->handler);
	}
	
	/**
	 * Sets the stream pointer to end of stream.
	 * @return bool Was the operation successful?
	 */
	public function forward(): bool
	{
		return fseek($this->handler, 0, SEEK_END);
	}
	
	/**
	 * Allows the creation of a simple reader and writer model by supporting a
	 * portable way of locking complete streams in an advisory way.
	 * @param bool $share Should lock be a shared one, for reading?
	 * @param bool $blocking Should execution block while locking?
	 * @return bool Was lock successfully applied?
	 * @see Stream::unlock()
	 */
	public function lock(bool $share = false, bool $blocking = false): bool
	{
		$lock = ($share ? LOCK_SH : LOCK_EX) | ($blocking ? 0 : LOCK_NB);

		return stream_supports_lock($this->handler)
		    && flock($this->handler, $lock);
	}
	
	/**
	 * Retrieves some metadata about stream. This class exposes the following
	 * metadata: wrapper, header, timeout, type, blocked, seekable.
	 * @param string $data Data name to retrieve.
	 * @return mixed The metadata value.
	 */
	public function metadata(string $data)
	{
		static $metadata = [
			'wrapper' => 'wrapper_type',
			'header' => 'wrapper_data',
			'seekable' => 'seekable',
			'timeout' => 'timed_out',
			'type' => 'stream_type',
			'blocked' => 'blocked',
		];
		
		$data = $metadata[$data] ?? null;
		return stream_get_meta_data($this->handler)[$data] ?? null;
	}
	
	/**
	 * Informs the mode this stream has been opened with.
	 * @return string The stream access mode.
	 */
	public function mode(): string
	{
		return stream_get_meta_data($this->handler)['mode'];
	}
	
	/**
	 * Offsets stream pointer by given amount.
	 * @param int $offset Number of bytes to offset.
	 * @return bool Was the operation successful?
	 */
	public function offset(int $offset): bool
	{
		return (bool)fseek($this->handler, $offset, SEEK_CUR);
	}
	
	/**
	 * Writes all of stream contents to output buffer, from current read point
	 * until end-of-file is reached.
	 * @return int Number of characters sent to output from stream.
	 */
	public function passthru(): int
	{
		return fpassthru($this->handler);
	}
	
	/**
	 * Writes a formatted string to stream.
	 * @param string $format String format to apply to data.
	 * @param mixed ...$vars Data to format and write to stream.
	 * @return int Number of bytes written to stream.
	 */
	public function printf(string $format, ...$vars): int
	{
		return fprintf($this->handler, $format, ...$vars);
	}
	
	/**
	 * Gives access to object's raw contents. That is, it exposes the internal
	 * stream that is usually wrapped by this object instance.
	 * @return resource The raw stream resource.
	 */
	public function raw()
	{
		return $this->handler;
	}
	
	/**
	 * Retrieves contents from stream.
	 * @param int $length Maximum number of bytes to read from stream.
	 * @return string Read stream contents.
	 */
	public function read(int $length = null): string
	{
		return stream_get_contents($this->handler, $length ?: -1);
	}
	
	/**
	 * Retrieves a line from stream.
	 * @param int $length Maximum length of line to read from stream.
	 * @return string Retrieved stream contents.
	 */
	public function readline(int $length = null): string
	{
		return fgets($this->handler, $length ?: 8192);
	}
	
	/**
	 * Retrieves contents from stream and puts it into another stream.
	 * @param resource|StreamInterface $target Target to put content on.
	 * @param int $length Maximum number of bytes to retrieve from stream.
	 * @return int Length of data read out of stream.
	 */
	public function readto($target, int $length = null): int
	{
		return $target instanceof StreamInterface
			? $target->write($this->read($length))
			: stream_copy_to_stream($this->handler, $target, $length ?: -1);
	}
	
	/**
	 * Sets stream pointer to the beginning of stream.
	 * @return bool Was the operation successful?
	 */
	public function rewind(): bool
	{
		return fseek($this->handler, 0, SEEK_SET);
	}
	
	/**
	 * Parses one line input from stream according to given format.
	 * @param string $format String format to extract data from stream.
	 * @param mixed ...$vars Variables to receive parsed values.
	 * @return mixed If no $vars given, returns an array of parsed values.
	 */
	public function scanf(string $format, &...$vars)
	{
		return fscanf($this->handler, $format, ...$vars);
	}
	
	/**
	 * Seeks the stream pointer.
	 * @param int $offset The position to seek.
	 * @return bool Was the operation successful?
	 */
	public function seek(int $offset): bool
	{
		return (bool)fseek($this->handler, $offset, SEEK_SET);
	}
	
	/**
	 * Tells the current stream pointer position.
	 * @return int The current stream pointer offset.
	 */
	public function tell(): int
	{
		return ftell($this->handler);
	}
	
	/**
	 * Truncates stream to given length. If given length is larger than stream,
	 * it is extended with null bytes.
	 * @param int $size The size to truncate to.
	 * @return bool Was the operation successful?
	 */
	public function truncate(int $size): bool
	{
		return ftruncate($this->handler, $size);
	}
	
	/**
	 * Unlocks a previously locked stream.
	 * @return bool Was the operation successful?
	 * @see Stream::lock()
	 */
	public function unlock(): bool
	{
		return stream_supports_lock($this->handler)
			&& flock($this->handler, LOCK_UN | LOCK_NB);
	}
	
	/**
	 * Informs locator used for instantiating this stream.
	 * @return string The locator of this stream.
	 */
	public function url(): string
	{
		return stream_get_meta_data($this->handler)['uri'];
	}
	
	/**
	 * Writes contents to stream.
	 * @param string $content Content to write to stream.
	 * @param int $length Maximum length of data to write to stream.
	 * @return int Length of data written to stream.
	 */
	public function write(string $content, int $length = null): int
	{
		return is_null($length)
			? fwrite($this->handler, $content)
			: fwrite($this->handler, $content, $length);
	}
	
	/**
	 * Retrieves content from stream and writes it to another stream.
	 * @param resource|StreamInterface $source Source content is retrieved from.
	 * @param int $length Maximum number of bytes to be written to stream.
	 * @return int Total length of data written to stream.
	 */
	public function writefrom($source, int $length = null): int
	{
		return $source instanceof StreamInterface
			? $this->write($source->read($length))
			: stream_copy_to_stream($source, $this->handler, $length ?: -1);
	}
	
	/**
	 * Creates an instance of a virtual stream. Virtual streams are stored in
	 * memory and may be transfered to a temporary file if a given size
	 * threshold is reached.
	 * @param string $content Initial stream contents.
	 * @return StreamInterface The virtual stream instance.
	 */
	public static function virtual(string $content = null): StreamInterface
	{
		$stream = new static('php://temp', 'r+');
		
		if(!is_null($content) && $stream->write($content))
			$stream->rewind();
		
		return $stream;
	}
}
