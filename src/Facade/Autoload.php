<?php
/**
 * Zettacast\Facade\Autoload class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Facade;

use Zettacast\Helper\FacadeAbstract;
use Zettacast\Autoload\LoaderInterface;
use Zettacast\Autoload\Autoload as baseclass;

/**
 * Zettacast's Autoload façade class.
 * This class exposes the Autoload package methods to external usage.
 * @method static register(LoaderInterface $loader): bool
 * @method static unregister(LoaderInterface $loader): void
 * @method static isRegistered(LoaderInterface $loader): bool
 * @todo Allow using a name to refer for specific loaders.
 * @todo Add an ::alias method, so classes can be aliased.
 * @version 1.0
 */
class Autoload extends FacadeAbstract
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
