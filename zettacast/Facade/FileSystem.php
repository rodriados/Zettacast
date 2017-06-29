<?php
/**
 * FileSystem façade file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Facade;

use Zettacast\Helper\Facadable;
use Zettacast\FileSystem\FileSystem as baseclass;

final class FileSystem {

	use Facadable;
	
	protected static function accessor()
	{
		return baseclass::class;
	}
	
}
