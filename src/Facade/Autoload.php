<?php
/**
 * Autoload façade file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Facade;

use Zettacast\Autoload\Loader\Space;
use Zettacast\Helper\Facadable;
use Zettacast\Autoload\Loader\Alias as AliasLoader;
use Zettacast\Autoload\Loader\Space as SpaceLoader;
use Zettacast\Autoload\Loader\Object as ObjectLoader;
use Zettacast\Autoload\Autoload as baseclass;

/**
 * Zettacast's Autoload façade class.
 * This class exposes package:autoload methods to external usage.
 * @version 1.0
 */
final class Autoload
{
	use Facadable;
	
	/**
	 * Aliased objects loader instance.
	 * @var AliasLoader Loader instance.
	 */
	private static $alias = null;
	
	/**
	 * External objects loader instance.
	 * @var ObjectLoader Loader instance.
	 */
	private static $object = null;
	
	/**
	 * Namespaced objects loader instance.
	 * @var SpaceLoader Loader instance.
	 */
	private static $space = null;
	
	/**
	 * Informs what the façaded object accessor is, allowing it to be further
	 * used when calling façaded methods.
	 * @return mixed Façaded object accessor.
	 */
	protected static function accessor()
	{
		return baseclass::class;
	}
	
	/**
	 * Allows the registration of the aliased objects loader.
	 * @param array $map Objects alias mapping to be set in the loader.
	 * @return AliasLoader The loader instance.
	 */
	public static function alias(array $map = [])
	{
		if(empty($map))
			return self::$alias;
		
		if(!isset(self::$alias))
			self::facaded()->register(self::$alias = new AliasLoader);
		
		foreach($map as $alias => $target)
			self::$alias->set($alias, $target);

		return self::$alias;
	}
	
	/**
	 * Allows the registration of the objects loader.
	 * @param array|null $map Objects mapping to be set in the loader.
	 * @return ObjectLoader The loader instance.
	 */
	public static function class(array $map = [])
	{
		if(empty($map))
			return self::$object;
		
		if(!isset(self::$object))
			self::facaded()->register(self::$object = new ObjectLoader);
		
		foreach($map as $obj => $file)
			self::$object->set($obj, $file);

		return self::$object;
	}
	
	/**
	 * Allows the registration of the namespaces loader.
	 * @param array|null $map Namespaces mapping to be set in the loader.
	 * @return SpaceLoader The loader instance.
	 */
	public static function namespace(array $map = [])
	{
		if(empty($map))
			return self::$space;
		
		if(!isset(self::$space))
			self::facaded()->register(self::$space = new SpaceLoader);
		
		foreach($map as $space => $folder)
			self::$space->set($space, $folder);
		
		return self::$space;
	}
	
}
