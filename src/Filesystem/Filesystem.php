<?php
/**
 * Zettacast\Filesystem\Filesystem class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Filesystem;

use Zettacast\Filesystem\Driver\Local as LocalDriver;
use Zettacast\Filesystem\Driver\Virtual as VirtualDriver;

/**
 * This class acts as wrapper to a local driver.
 * @package Zettacast\Filesystem
 * @version 1.0
 */
class Filesystem
	extends LocalDriver
{
	/**
	 * Local driver constructor.
	 * @param string $root Root directory for all operations done in driver.
	 */
	public function __construct(string $root = DOCROOT)
	{
		parent::__construct($root);
	}
	
	/**
	 * Creates a new virtual driver, to be removed at this object destruction.
	 * @return VirtualDriver New virtual filesystem.
	 */
	public static function virtual()
	{
		return new VirtualDriver;
	}
	
}
