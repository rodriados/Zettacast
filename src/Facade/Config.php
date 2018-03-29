<?php
/**
 * Config façade file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Facade;

/**
 * Zettacast's Config façade class.
 * This class creates a methods for easily accessing configuration files.
 * @method static get(string $key, $default = null)
 * @method static has(string $key): bool
 * @method static load(string $file): bool
 * @version 1.0
 */
final class Config extends Facade
{
	/**
	 * Informs what the façaded object accessor is, allowing it to be further
	 * used when calling façaded methods.
	 * @return mixed Façaded object accessor.
	 */
	protected  static function accessor()
	{
		return 'config';
	}
}
