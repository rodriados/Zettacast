<?php
/**
 * Zettacast\Filesystem\Filesystem class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Filesystem;

use Zettacast\Filesystem\Disk\LocalDisk;
use Zettacast\Filesystem\Disk\VirtualDisk;

/**
 * This class acts as wrapper to a local disk.
 * @package Zettacast\Filesystem
 * @version 1.0
 */
class Filesystem extends LocalDisk
{
	/**
	 * Filesystem constructor.
	 * Creates the local disk instance.
	 * @param string $root Root directory for all operations done in disk.
	 */
	public function __construct(string $root = ROOTPATH)
	{
		parent::__construct($root);
	}
	
	/**
	 * Creates a new virtual disk, to be erased at this object destruction.
	 * @return VirtualDisk New virtual filesystem.
	 */
	public static function virtual()
	{
		return new VirtualDisk;
	}
}
