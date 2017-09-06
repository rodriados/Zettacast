<?php
/**
 * Config façade file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Facade;

use Zettacast\Helper\Facade;
use Zettacast\Config\Repository as baseclass;

/**
 * Zettacast's Config façade class.
 * This class exposes package:config\Repository methods to external usage.
 * @method static mixed get(string $key, $default)
 * @version 1.0
 */
final class Config
{
	use Facade;
	
	/**
	 * Informs what the façaded object accessor is, allowing it to be further
	 * used when calling façaded methods.
	 * @return mixed Façaded object accessor.
	 */
	protected static function accessor()
	{
		return new baseclass(APPPATH.'/config');
	}
	
}
