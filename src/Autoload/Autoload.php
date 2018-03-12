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

/**
 * The autoloader is responsible for loading all objects required by the
 * application or the framework itself automatically. To be able to do so, the
 * autoloader requires its lookup folders to be explicitly defined.
 * @package Zettacast\Autoload
 * @version 1.0
 */
final class Autoload
{
	/**
	 * Stores the loaders already registered in the autoloading system. This
	 * allows us to keep track of all object loader functions.
	 * @var LoaderInterface[] Object loader functions registered.
	 */
	private $loaders;
	
	/**
	 * Stores the default loader instance for Zettacast objects. This loader is
	 * special and cannot be closed nor unregistered.
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
	 * Registers a loader in autoload stack. The autoload function will be
	 * responsible for automatically loading all objects invoked by framework
	 * or by application.
	 * @var LoaderInterface $loader A loader to register.
	 * @return bool Was the loader successfully registered?
	 */
	public function register(LoaderInterface $loader): bool
	{
		if($this->isRegistered($loader))
			return false;
		
		$this->loaders[spl_object_hash($loader)] = true;
		return spl_autoload_register([$loader, 'load']);
	}
	
	/**
	 * Unregisters a object loader from autoload stack.
	 * @param LoaderInterface $loader A loader to unregister.
	 */
	public function unregister(LoaderInterface $loader): void
	{
		if($this->isRegistered($loader)) {
			unset($this->loaders[spl_object_hash($loader)]);
			spl_autoload_unregister([$loader, 'load']);
		}
	}
	
	/**
	 * Checks whether a loader has already been registered in stack.
	 * @param LoaderInterface $loader Target to check whether registered.
	 * @return bool Is loader already registered?
	 */
	public function isRegistered(LoaderInterface $loader): bool
	{
		return (bool)($this->loaders[spl_object_hash($loader)] ?? false);
	}
}
