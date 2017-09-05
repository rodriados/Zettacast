<?php
/**
 * Zettacast\Filesystem\Stream\Virtual class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Filesystem\Stream;

/**
 * This class handles all interactions to a virtual stream.
 * @package Zettacast\Filesystem\Stream
 * @version 1.0
 */
class Virtual
	extends Stream
{
	/**
	 * Virtual stream constructor.
	 * @param string $contents Initial stream contents.
	 */
	public function __construct(string $contents = null)
	{
		parent::__construct('php://temp', 'r+');
		!is_null($contents) && $this->write($contents);
		$this->rewind();
	}
	
}
