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
 * @method static register(LoaderInterface $loader): bool
 * @method static registered(LoaderInterface $loader): bool
 * @method static unregister(LoaderInterface $loader): void
 */
class Autoload extends FacadeAbstract
{
	/**
	 * @inheritdoc
	 */
	protected static function accessor()
	{
		return baseclass::class;
	}
}
