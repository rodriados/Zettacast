<?php
/**
 * Autoload façade file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Facade;

use Zettacast\Autoload\Loader\Alias;
use Zettacast\Autoload\Loader\Object;
use Zettacast\Autoload\Loader\Space;
use Zettacast\Helper\Contract\Facadable;
use Zettacast\Autoload as baseclass;

/**
 * Zettacast's Autoload façade class.
 * This class exposes package:autoload methods to external usage.
 * @version 1.0
 */
final class Autoload {
	
	use Facadable;
	
	/**
	 * Aliased objects loader instance.
	 * @var Alias Loader instance.
	 */
	private static $alias = null;
	
	/**
	 * External objects loader instance.
	 * @var Object Loader instance.
	 */
	private static $object = null;
	
	/**
	 * Namespaced objects loader instance.
	 * @var Space Loader instance.
	 */
	private static $space = null;
	
	/**
	 * Informs what the façaded object accessor is, allowing it to be further
	 * used when calling façaded methods.
	 * @return mixed Façaded object accessor.
	 */
	protected static function accessor() {
		
		return baseclass::class;
		
	}
	
	/**
	 * Allows the registration of the aliased objects loader.
	 * @param array|null $map Objects mapping to be set in the loader.
	 * @return Alias The loader instance.
	 */
	public static function alias(array $map = null) {
		
		if($map)
			return self::alias()->set($map);
		
		if(is_null(self::$alias))
			zetta(baseclass::class)->register(self::$alias = new Alias);
		
		return self::$alias;
		
	}
	
	/**
	 * Allows the registration of the objects loader.
	 * @param array|null $map Objects mapping to be set in the loader.
	 * @return Object The loader instance.
	 */
	public static function object(array $map = null) {
		
		if($map)
			return self::object()->set($map);
		
		if(is_null(self::$object))
			zetta(baseclass::class)->register(self::$object = new Object);
		
		return self::$object;
		
	}
	
	/**
	 * Allows the registration of the namespaces loader.
	 * @param array|null $map Namespaces mapping to be set in the loader.
	 * @return Space The loader instance.
	 */
	public static function space(array $map = null) {
		
		if($map)
			return self::space()->set($map);
		
		if(is_null(self::$space))
			zetta(baseclass::class)->register(self::$space = new Space);
		
		return self::$space;
		
	}
	
}
