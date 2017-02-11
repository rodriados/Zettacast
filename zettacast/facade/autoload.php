<?php
/**
 * Autoload façade file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast;

use Zettacast\Autoload\Loader\Base;
use Zettacast\Autoload\Loader\Alias;
use Zettacast\Autoload\Loader\Object;
use Zettacast\Autoload\Loader\Space;
use Zettacast\Helper\Contract\Singleton;
use Zettacast\Autoload\Autoload as baseclass;

/**
 * Zettacast's Autoload façade class.
 * This class exposes package:autoload methods to external usage.
 * @version 1.0
 */
final class Autoload {
	
	/*
	 * Singleton trait inclusion. This trait implements Singleton pattern
	 * that allows the existance of one and only one object instance.
	 */
	use Singleton;
	
	/**
	 * Aliased objects loader instance.
	 * @var Alias Loader instance.
	 */
	private $alias = null;
	
	/**
	 * External objects loader instance.
	 * @var Object Loader instance.
	 */
	private $object = null;
	
	/**
	 * Namespaced objects loader instance.
	 * @var Space Loader instance.
	 */
	private $space = null;
	
	/**
	 * Registers a loader to the autoload stack. The autoload function will be
	 * the responsible for automatically loading all classes invoked by the
	 * framework or by the application.
	 * @var Base $loader A loader to be registered.
	 * @return bool Was the loader successfully registered?
	 */
	public static function register(Base $loader) {
		
		return baseclass::register($loader);
		
	}
	
	/**
	 * Unregisters a class loader from the autoload stack.
	 * @param Base $loader A loader to be unregistered.
	 */
	public static function unregister(Base $loader) {
		
		baseclass::unregister($loader);
		
	}
	
	/**
	 * Resets all registered loaders and unregister all loaders but the default
	 * one. This is used when only Zettacast's core classes are needed.
	 */
	public static function reset() {
		
		baseclass::reset();
		
	}
	
	/**
	 * Allows the registration of the aliased objects loader.
	 * @param array|null $map Objects mapping to be set in the loader.
	 * @return Alias The loader instance.
	 */
	public static function alias(array $map = null) {
		
		if($map)
			return self::alias()->set($map);
		
		if(is_null(self::i()->alias))
			baseclass::register(self::i()->alias = new Alias);
		
		return self::i()->alias;
		
	}
	
	/**
	 * Allows the registration of the objects loader.
	 * @param array|null $map Objects mapping to be set in the loader.
	 * @return Object The loader instance.
	 */
	public static function object(array $map = null) {
		
		if($map)
			return self::object()->set($map);
		
		if(is_null(self::i()->object))
			baseclass::register(self::i()->object = new Object);
		
		return self::i()->object;
		
	}
	
	/**
	 * Allows the registration of the namespaces loader.
	 * @param array|null $map Namespaces mapping to be set in the loader.
	 * @return Space The loader instance.
	 */
	public static function space(array $map = null) {
		
		if($map)
			return self::space()->set($map);
		
		if(is_null(self::i()->space))
			baseclass::register(self::i()->space = new Space);
		
		return self::i()->space;
		
	}
	
}
