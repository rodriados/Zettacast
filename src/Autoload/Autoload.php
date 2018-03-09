<?php
/**
 * Zettacast\Autoload\Autoload class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Autoload;

require SRCPATH.'/Autoload/LoaderInterface.php';
require SRCPATH.'/Autoload/Loader/InternalLoader.php';

use Zettacast\Autoload\Loader\InternalLoader;

final class Autoload
{
	/**
	 * Stores the loaders already registered in the autoloading system. This
	 * allows us to keep track of all class loading functions.
	 * @var LoaderInterface[] Class loader functions registered.
	 */
	private $loaders;
	
	/**
	 * Stores the default loader instance for Zettacast classes. This loader is
	 * special and cannot be closed.
	 * @var InternalLoader Zettacast main loader instance.
	 */
	private $internal;
	
	/**
	 * Autoload constructor.
	 * Initializes the object and set values to instance properties.
	 */
	public function __construct()
	{
		$this->loaders = [];
		$this->internal = new InternalLoader;
		
		$this->register($this->internal);
	}
	
	/**
	 * Registers a loader to the autoload stack. The autoload function will be
	 * the responsible for automatically loading all classes invoked by the
	 * framework or by the application.
	 * @var LoaderInterface $loader A loader to be registered.
	 * @return bool Was the loader successfully registered?
	 */
	public function register(LoaderInterface $loader): bool
	{
		if($this->registered($loader))
			return false;
		
		$this->loaders[spl_object_hash($loader)] = true;
		return spl_autoload_register([$loader, 'load']);
	}
	
	/**
	 * Checks whether a loader has already been registered to the stack.
	 * @param LoaderInterface $loader Target to check whether registered.
	 * @return bool Is loader already registered?
	 */
	public function registered(LoaderInterface $loader): bool
	{
		return (bool)($this->loaders[spl_object_hash($loader)] ?? false);
	}
	
	/**
	 * Unregisters a class loader from the autoload stack.
	 * @param LoaderInterface $loader A loader to be unregistered.
	 */
	public function unregister(LoaderInterface $loader): void
	{
		if($this->registered($loader)) {
			unset($this->loaders[spl_object_hash($loader)]);
			spl_autoload_unregister([$loader, 'load']);
		}
	}
}
