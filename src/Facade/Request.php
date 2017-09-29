<?php
/**
 * Request façade file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Facade;

use Zettacast\Helper\Facade;
use Zettacast\Contract\Http\Request as baseclass;

/**
 * Zettacast's Request façade class.
 * This class exposes package:http\Request methods to external usage.
 * @version 1.0
 */
final class Request extends Facade
{
	/**
	 * Informs what the façaded object accessor is, allowing it to be further
	 * used when calling façaded methods.
	 * @return mixed Façaded object accessor.
	 */
	protected static function accessor()
	{
		return baseclass::class;
	}
	
}
