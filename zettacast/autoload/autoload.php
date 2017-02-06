<?php
/**
 * Autoload proxy file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast;

/*
 * Imports class-loaders "manually". This is the last time we do this kind of
 * "hardcoding". All future classes will be loaded automatically.
 */
require FWORKPATH.'/autoload/loader.php';
require FWORKPATH.'/autoload/loader/initial.php';
require FWORKPATH.'/autoload/loader/alias.php';

/**
 * The autoload class is responsible for loading all classes required by the
 * framework or the application itself. It also lets you set explicit paths for
 * classes to be loaded from.
 * @version 1.0
 */
final class Autoload {
	
	/**
	 * Stores the classloaders already registered in the autoloading system.
	 * This allows us to keep track of all class loading functions.
	 * @var array Class loader functions registered.
	 */
	private $loaders;
	
	/**
	 * Stores the default loader instance for Zettacast classes. This loader is
	 * special and cannot be closed.
	 * @var Autoload\Loader\Initial Zettacast main loader instance.
	 */
	private $default;
	
	/**
	 * Stores the alias loader instance for Application use. This loader is
	 * only instanciated when needed.
	 * @var Autoload\Loader\Alias Zettacast class alias loader instance.
	 */
	private $alias = null;
	
	
	/**
	 * Stores this class' singleton instance and helps checking whether the
	 * class has already been initialized or not.
	 * @var Autoload Class' singleton instance.
	 */
	private static $i = null;
	
	/**
	 * Autoload constructor. Initializes the class and set values to instance
	 * properties.
	 */
	private function __construct() {
		
		$this->loaders = [];
		$this->default = new Autoload\Loader\Initial;
		
	}
	
	/**
	 * Registers a loader to the autoload stack. The autoload function will be
	 * the responsible for automatically loading all classes invoked by the
	 * framework or by the application.
	 * @var Autoload\Loader $loader A loader to be registered.
	 * @return bool Was the loader successfully registered?
	 */
	public static function register(Autoload\Loader $loader = null) {
		
		if(!isset(self::$i)) {
			self::$i = new self;
			return self::register(self::$i->default);
		}
		
		if(!in_array($loader, self::$i->loaders)) {
			self::$i->loaders[] = $loader;
			return spl_autoload_register([$loader, 'load']);
		}
		
		return false;
		
	}
	
	/**
	 * Unregisters a class loader from the autoload stack.
	 * @param Autoload\Loader $loader A loader to be unregistered.
	 */
	public static function unregister(Autoload\Loader $loader) {
		
		if(in_array($loader, self::$i->loaders)) {
			unset(self::$i->loaders[array_search($loader, self::$i->loaders)]);
			spl_autoload_unregister([$loader, 'load']);
		}
		
	}
	
	/**
	 * Resets all registered loaders and unregister all loaders but the default
	 * one. This is used when only Zettacast's core classes are needed.
	 */
	public static function reset() {
		
		foreach(self::$i->loaders as $loader) {
			spl_autoload_unregister([$loader, 'load']);
			$loader->reset();
		}
		
		self::register(self::$i->default);
		
	}
	
	/**
	 * Adds new map entries to the map of classes. Conflicting entries will
	 * be simply overwritten to the newest value.
	 * @param array $map Map of classes to be added.
	 */
	public static function addClass(array $map) {
		
		self::$i->default->addClass($map);
		
	}
	
	/**
	 * Removes a class from the map of classes. The target class will not be
	 * unloaded in case it has already been loaded, but it will not be able to
	 * load in case it still hasn't.
	 * @param array|string $class Class to removed from map of classes.
	 */
	public static function delClass(array $class) {
		
		self::$i->default->delClass($class);
		
	}
	
	/**
	 * Adds new map entries to the map of namespaces. Conflicting entries
	 * will simply be overwritten to the newest value.
	 * @param array $map Map of namespaces to be added.
	 */
	public static function addNamespace(array $map) {
		
		self::$i->default->addNamespace($map);
		
	}
	
	/**
	 * Removes a namespace from the map of namespaces. Classes in the target
	 * namespace will not be unloaded in case they have already been loaded,
	 * but they will not be able to load in case they still haven't.
	 * @param array|string $nspace Namespace to be removed from map.
	 */
	public static function delNamespace(array $nspace) {
		
		self::$i->default->delNamespace($nspace);
		
	}
	
	/**
	 * Adds new alias map entries. Conflicting entries will simply be
	 * overwritten to the newest value.
	 * @param array $map Map of aliases to be added.
	 */
	public static function addAlias(array $map) {
		
		if(!empty($map) and !isset(self::$i->alias)) {
			
			self::$i->alias = new Autoload\Loader\Alias;
			self::register(self::$i->alias);
			
		}
		
		empty($map) or self::$i->alias->addAlias($map);
		
	}
	
	/**
	 * Removes an alias from the map. Classes loaded using the target alias
	 * will not be unloaded in they have already been loaded, but they will
	 * not be able to be loaded using alias anymore.
	 * @param array|string $alias Alias to be removed.
	 */
	public static function delAlias($alias) {
		
		if(isset(self::$i->alias))
			self::$i->alias->delAlias($alias);
		
	}
	
	/**
	 * Erases all previous aliases and put new ones in the list.
	 * @param array $map New alias mappings.
	 */
	public static function setAlias(array $map) {
		
		if(!empty($map) and !isset(self::$i->alias)) {
			
			self::$i->alias = new Autoload\Loader\Alias;
			self::register(self::$i->alias);
			
		}
		
		empty($map) or self::$i->alias->setAlias($map);
	}
	
}
