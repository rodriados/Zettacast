<?php
/**
 * Zettacast\Contract\Stream\AbstractFilter abstract class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Contract\Stream;

/**
 * This abstract holds all properties and methods needed for a stream filter to
 * be successfully built and registrable.
 * @property resource $stream Stream being filtered.
 * @package Zettacast\Stream
 * @version 1.0
 */
abstract class AbstractFilter extends \php_user_filter
{
	/**#@+
	 * Filter return constants. It is required this filter returns one these
	 * values whenever invoked. PHP may take different actions depending on the
	 * returned value.
	 * @var int Filter status values.
	 */
	const SUCCESS = PSFS_PASS_ON;
	const WAITING = PSFS_FEED_ME;
	const ERROR = PSFS_ERR_FATAL;
	/**#@-*/
	
	/**
	 * Incoming stream brigade.
	 * @var resource Input of data to be filtered.
	 */
	protected $input;
	
	/**
	 * Outgoing stream brigade.
	 * @var resource Filtered data output.
	 */
	protected $output;
	
	/**
	 * Incoming stream consumption counter.
	 * @var int& Incoming data consumed bytes count.
	 */
	protected $count;
	
	/**
	 * Indicates how much data of the current bucket have already been
	 * consumed. In other ways, this informs the current iteration position.
	 * @var int Bucket data pointer.
	 */
	private $pointer = 0;
	
	/**
	 * Currently active bucket. The stream brigades are composed of a chain of
	 * buckets. This property, holds the currently active input bucket.
	 * @var object Bucket being currently consumed.
	 */
	private $bucket = null;
	
	/**
	 * This constructor is actually called by AbstractFilter::onCreate. We do
	 * it so filter objects can be consistent with every other object of the
	 * project, that have their constructors called automatically.
	 * @param string $filtername The name the filter was instantiated with.
	 * @param mixed $params The parameters given to filter when instantiating.
	 */
	public function __construct(string $filtername, $params = null)
	{
		# Actually, the $filtername and $params properties are already set.
		# Given so, our default constructor has it's job already done.
	}
	
	/**
	 * Captures a bucket from incoming stream brigade, and makes it writable.
	 * @return object|bool The created writeable bucket.
	 */
	protected function capture()
	{
		if($bucket = stream_bucket_make_writeable($this->input)) {
			$this->count += $bucket->datalen;
			$this->pointer = 0;
		}
		
		return $bucket;
	}
	
	/**
	 * Prepends a bucket to the input stream brigade.
	 * @param object|resource $bucket Bucket to be prepended to brigade.
	 */
	protected function return($bucket)
	{
		stream_bucket_prepend($this->input, $bucket);
	}
	
	/**
	 * Appends a bucket to the output stream brigade.
	 * @param object $bucket Bucket to be appended to brigade.
	 */
	protected function send($bucket)
	{
		stream_bucket_append($this->output, $bucket);
	}
	
	/**
	 * Retrieves content from the incoming stream brigade.
	 * @param int $length Maximum number of bytes to be retrieved.
	 * @return string|bool Retrieved stream contents.
	 */
	protected function read(int $length = 2048)
	{
		$result = false;

		if($length <= 0)
			return $result;
		
		while($this->valid()) {
			$patch = substr($this->bucket->data, $this->pointer, $length);
			$bytes = strlen($patch);
			
			$this->pointer += $bytes;
			$length -= $bytes;
			$result .= $patch;
		}
		
		return $result;
	}
	
	/**
	 * Retrieves a line from the incoming stream brigade.
	 * @param int $length Maximum number of bytes to be retrieved.
	 * @return string|bool Retrieved stream contents.
	 */
	protected function readLine(int $length = 2048)
	{
		$result = false;
		
		if($length <= 0)
			return $result;
		
		while($this->valid()) {
			$patch = substr($this->bucket->data, $this->pointer, $length);

			if(($endlp = strpos($patch, "\n")) !== false)
				$patch = substr($patch, 0, $endlp + 1);
			
			$bytes = strlen($patch);
			$this->pointer += $bytes;
			$length -= $bytes;
			$result .= $patch;
			
			if($endlp !== false)
				break;
		}
		
		return $result;
	}
	
	/**
	 * Virtually puts data back to the input stream brigade. The data may not
	 * be the same previously read. In any case, the next read operation will
	 * retrieve the recalled data.
	 * @param string $data Content to be unread.
	 */
	protected function unread(string $data)
	{
		$bucket = stream_bucket_new($this->stream, $data);
		$this->return($bucket);
	}
	
	/**
	 * Writes contents to the outgoing stream brigade.
	 * @param string $data Content to be written.
	 */
	protected function write(string $data)
	{
		$bucket = stream_bucket_new($this->stream, $data);
		$this->send($bucket);
	}
	
	/**
	 * Checks whether the incoming stream brigade still has data to processed.
	 * @return bool Is there data left to be processed?
	 */
	protected function valid(): bool
	{
		return (!$this->bucket || $this->pointer >= $this->bucket->datalen)
			? (bool)($this->bucket = $this->capture())
			: true;
	}
	
	/**
	 * This method is called during instantiation of the filter object, instead
	 * of the constructor. For that reason, we simply delegate this method to
	 * the actual constructor.
	 * @see AbstractFilter::__construct
	 */
	final public function onCreate()
	{
		$this->__construct($this->filtername, $this->params);
	}
	
	/**
	 * This method is called upon filter shutdown, which typically is also
	 * during stream shutdown. But, we cannot rely on this method being called,
	 * as it is not always called when the filter is terminated.
	 * @see AbstractFilter::__destruct
	 */
	final public function onClose()
	{
		# As this method cannot be relied on, we delegate it to the default
		# destructor, which in turn, is always called by the time the filter is
		# being shut down.
	}
	
	/**
	 * This method gets called whenever the filter is invoked by the stream.
	 * @param resource $in Incoming stream brigade, containing data to filter.
	 * @param resource $out Outgoing stream brigade, where filtered data go to.
	 * @param int &$count Incoming stream consumption counter.
	 * @param bool $closing Informs whether the filter is closing or not.
	 * @return int Filter returning value.
	 */
	final public function filter($in, $out, &$count, $closing): int
	{
		$this->input = $in;
		$this->output = $out;
		$this->count = &$count;
		
		return $this->process($closing);
	}
	
	/**
	 * Registers this class as a stream filter. If no name is given, the filter
	 * class will be registered using its fully qualified name as filter name.
	 * @param string $filtername Filter name to be given to this class.
	 * @return bool Was the filter successfully registered?
	 */
	final public static function register(string $filtername = null): bool
	{
		return stream_filter_register(
			$filtername ?: static::class,
			static::class
		);
	}
	
	/**
	 * This method is responsible for processing the data to data to be
	 * filtered. It is invoked automatically by the stream whenever it needs
	 * data to be filtered.
	 * @param bool $closing Informs whether the filter is closing or not.
	 * @return int Filtering status value.
	 */
	abstract protected function process(bool $closing): int;
	
}
