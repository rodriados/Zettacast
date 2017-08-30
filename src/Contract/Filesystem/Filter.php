<?php
/**
 * Zettacast\Contract\Filesystem\Filter abstract class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Contract\Filesystem;

use Zettacast\Filesystem\Stream\Filter as StreamFilter;

/**
 * This abstract holds all properties and methods needed for a stream filter to
 * be successfully built and registrable.
 * @property string $filtername Name used to instantiate filter.
 * @property mixed $params Parameters used when instantiating filter.
 * @property resource $stream Stream being filtered.
 * @package Zettacast\Contract\Filesystem
 * @version 1.0
 */
abstract class Filter
	extends \php_user_filter
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
	protected $in;
	
	/**
	 * Outgoing stream brigade.
	 * @var resource Filtered data output.
	 */
	protected $out;
	
	/**
	 * Incoming stream consumption counter.
	 * @var int& Incoming data consumed bytes count.
	 */
	protected $count;
	
	/**
	 * Indicates whether the filter is being shut down.
	 * @var bool Is stream currently being closed, or freed from this filter?
	 */
	protected $closing;
	
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
	 * Method called by PHP whenever a stream must be filtered.
	 * @param resource $in Incoming stream brigade.
	 * @param resource $out Outgoing stream brigade.
	 * @param int &$count Incoming stream comsuption counter.
	 * @param bool $closing Whether the filter will be closed or not.
	 * @return int Filter returning value.
	 */
	final public function filter($in, $out, &$count, $closing) : int
	{
		$this->in = $in;
		$this->out = $out;
		$this->count = &$count;
		$this->closing = $closing;
		return $this->do();
	}
	
	/**
	 * This method is called during instantiation of the filter class object.
	 * If your filter allocates or initializes any other resources, such as a
	 * buffer, this is the place to do it.
	 */
	final public function onCreate()
	{
		$this->begin();
	}
	
	/**
	 * This method is called upon filter shutdown, which typically is also
	 * during stream shutdown. If any resources were allocated or initialized
	 * during creation this would be the time to destroy or dispose of them.
	 */
	final public function onClose()
	{
		$this->end();
	}
	
	/**
	 * Acquires a bucket from incoming stream brigade, and makes it available
	 * for writing.
	 * @return object The created writeable bucket.
	 */
	protected function acquire()
	{
		if($bucket = stream_bucket_make_writeable($this->in)) {
			$this->count += $bucket->datalen;
			$this->pointer = 0;
		}
		
		return $bucket;
	}
	
	/**
	 * This method is called by Filter::onCreate, whenever this filter is
	 * instantiated, and it is responsible for creating or allocating resources
	 * needed for filter functioning, if any.
	 */
	protected function begin()
	{
		;
	}
	
	/**
	 * Appends a bucket to the output stream brigade.
	 * @param object $bucket Bucket to be appended to brigade.
	 */
	protected function commit($bucket)
	{
		stream_bucket_append($this->out, $bucket);
	}
	
	/**
	 * Prepends a bucket to the input stream brigade.
	 * @param object|resource $bucket Bucket to be prepended to brigade.
	 */
	protected function devolve($bucket)
	{
		stream_bucket_prepend($this->in, $bucket);
	}
	
	/**
	 * This method is called by Filter::onClose, whenever this filter is
	 * about to be shutdown, and it is responsible for destroying or
	 * deallocating resources needed for filter functioning, if any.
	 */
	protected function end()
	{
		;
	}
	
	/**
	 * Retrieves content from the incoming stream brigade.
	 * @param int $length Maximum number of bytes to be retrieved.
	 * @return string Retrieved stream contents.
	 */
	protected function read(int $length = null) : string
	{
		if(!is_null($length) && $length <= 0)
			return false;
		
		$result = false;
		
		while($this->valid()) {
			$patch = !is_null($length)
				? substr($this->bucket->data, $this->pointer, $length)
				: substr($this->bucket->data, $this->pointer);
			
			$length = is_null($length) ? null : $length - strlen($patch);
			$this->pointer += strlen($patch);
			$result .= $patch;
		}
		
		return $result;
	}
	
	/**
	 * Retrieves a line from the incoming stream brigade.
	 * @param int $length Maximum number of bytes to be retrieved.
	 * @return string Retrieved stream contents.
	 */
	protected function readLine(int $length = null) : string
	{
		if(!is_null($length) && $length <= 0)
			return false;
		
		$result = false;
		
		while($this->valid()) {
			$patch = !is_null($length)
				? substr($this->bucket->data, $this->pointer, $length)
				: substr($this->bucket->data, $this->pointer);
			$endlp = strpos($patch, "\n");
			$patch = ($endlp !== false)
				? substr($patch, 0, $endlp + 1)
				: $patch;
			
			$this->pointer += strlen($patch);
			$result .= $patch;
			
			if($endlp !== false)
				break;
			
			$length = is_null($length) ? null : $length - strlen($patch);
		}
		
		return $result;
	}
	
	/**
	 * Virtually puts data back to the input stream brigade. The data may not
	 * be the same previously read. In any case, the next read operation will
	 * retrieve the recalled data.
	 * @param string $data Content to be unread.
	 */
	protected function recall(string $data)
	{
		$bucket = stream_bucket_new($this->stream, $data);
		$this->devolve($bucket);
	}
	
	/**
	 * Checks whether the incoming stream brigade still has data to processed.
	 * @return bool Is there data left to be processed?
	 */
	protected function valid() : bool
	{
		return (!$this->bucket || $this->pointer >= $this->bucket->datalen)
			? (bool)($this->bucket = $this->acquire())
			: true;
	}
	
	/**
	 * Writes contents to the outgoing stream brigade.
	 * @param string $data Content to be written.
	 */
	protected function write(string $data)
	{
		$bucket = stream_bucket_new($this->stream, $data);
		$this->commit($bucket);
	}
	
	/**
	 * Registers this class as a stream filter, with the given name.
	 * @param string $filtername Filter name to be given to this class.
	 * @return bool Was the filter successfully registered?
	 */
	final public static function register(string $filtername) : bool
	{
		return StreamFilter::register($filtername, static::class);
	}
	
	/**
	 * Method responsible for filtering the data. It is called whenever
	 * Filter::filter gets called.
	 * @return int Filtering status value.
	 */
	abstract protected function do() : int;
	
}
