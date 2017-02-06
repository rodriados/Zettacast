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
	public static function addclass(array $map) {
		
		self::$i->default->addclass($map);
		
	}
	
	/**
	 * Removes a class from the map of classes. The target class will not be
	 * unloaded in case it has already been loaded, but it will not be able to
	 * load in case it still hasn't.
	 * @param array|string $class Class to removed from map of classes.
	 */
	public static function delclass(array $class) {
		
		self::$i->default->delclass($class);
		
	}
	
	/**
	 * Adds new map entries to the map of namespaces. Conflicting entries
	 * will simply be overwritten to the newest value.
	 * @param array $map Map of namespaces to be added.
	 */
	public static function addnamespace(array $map) {
		
		self::$i->default->addnamespace($map);
		
	}
	
	/**
	 * Removes a namespace from the map of namespaces. Classes in the target
	 * namespace will not be unloaded in case they have already been loaded,
	 * but they will not be able to load in case they still haven't.
	 * @param array|string $nspace Namespace to be removed from map.
	 */
	public static function delnamespace(array $nspace) {
		
		self::$i->default->delnamespace($nspace);
		
	}
	
}
