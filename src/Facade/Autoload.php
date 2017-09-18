<?php
/**
 * Autoload façade file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Facade;

use Zettacast\Helper\Facade;
use Zettacast\Autoload\Loader\AliasLoader;
use Zettacast\Autoload\Loader\ObjectLoader;
use Zettacast\Autoload\Loader\NamespaceLoader;
use Zettacast\Autoload\Autoload as baseclass;

/**
 * Zettacast's Autoload façade class.
 * This class exposes package:autoload methods to external usage.
 * @version 1.0
 */
final class Autoload extends Facade
{
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
	 * @var NamespaceLoader Loader instance.
	 */
	private static $space = null;
	
	/**
	 * Allows the registration of the aliased objects loader.
	 * @param array $map Objects alias mapping to be set in the loader.
	 */
	public static function addAlias(array $map = [])
	{
		if(!isset(self::$alias) && !empty($map))
			self::facaded()->register(self::$alias = new AliasLoader);
		
		foreach($map as $alias => $target)
			self::$alias->set($alias, $target);
	}
	
	/**
	 * Allows the registration of the objects loader.
	 * @param array $map Object mappings to be set in the loader.
	 */
	public static function addClass(array $map = [])
	{
		if(!isset(self::$object) && !empty($map))
			self::facaded()->register(self::$object = new ObjectLoader);
		
		foreach($map as $obj => $file)
			self::$object->set($obj, $file);
	}
	
	/**
	 * Allows the registration of the namespaces loader.
	 * @param array $map Namespace mappings to be set in the loader.
	 */
	public static function addNamespace(array $map = [])
	{
		if(!isset(self::$space) && !empty($map))
			self::facaded()->register(self::$space = new NamespaceLoader);
		
		foreach($map as $space => $folder)
			self::$space->set($space, $folder);
	}
	
	/**
	 * Allows aliased objects to be removed from loader.
	 * @param string|array $target Aliases to be removed from loader.
	 */
	public static function delAlias($target)
	{
		if(isset(self::$alias))
			foreach(toArray($target) as $alias)
				self::$alias->del($alias);
	}
	
	/**
	 * Allows object mappings to be removed from loader.
	 * @param string|array $target Mappings to be removed from loader.
	 */
	public static function delClass($target)
	{
		if(isset(self::$object))
			foreach(toArray($target) as $obj)
				self::$object->del($obj);
	}
	
	/**
	 * Allows namespace mappings to be removed from loader.
	 * @param string|array $target Mappings to be removed from loader.
	 */
	public static function delNamespace($target)
	{
		if(isset(self::$space))
			foreach(toArray($target) as $space)
				self::$space->del($space);
	}
	
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
