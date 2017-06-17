<?php
/**
 * Zettacast\Autoload\Autoload class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Autoload;

require FWORKPATH.'/Autoload/Contract/Loader.php';
require FWORKPATH.'/Autoload/Loader/Framework.php';

use Zettacast\Autoload\Contract\Loader;
use Zettacast\Autoload\Loader\Framework;

/**
 * The autoload class is responsible for loading all classes required by the
 * framework or the application itself. It also lets you set explicit paths for
 * classes to be loaded from.
 * @package Zettacast\Autoload
 * @version 1.1
 */
final class Autoload
{
	/**
	 * Stores the classloaders already registered in the autoloading system.
	 * This allows us to keep track of all class loading functions.
	 * @var Loader[] Class loader functions registered.
	 */
	private $loaders;
	
	/**
	 * Stores the default loader instance for Zettacast classes. This loader is
	 * special and cannot be closed.
	 * @var Framework Zettacast main loader instance.
	 */
	private $framework;
	
	/**
	 * Autoload constructor.
	 * Initializes the class and set values to instance properties.
	 * @param string $path Framework's path.
	 */
	public function __construct(string $path = FWORKPATH)
	{
		$this->loaders = [];
		$this->framework = new Framework($path);
		$this->register($this->framework);
	}
	
	/**
	 * Registers a loader to the autoload stack. The autoload function will be
	 * the responsible for automatically loading all classes invoked by the
	 * framework or by the application.
	 * @var Loader $loader A loader to be registered.
	 * @return bool Was the loader successfully registered?
	 */
	public function register(Loader $loader)
	{
		if(!in_array($loader, $this->loaders)) {
			$this->loaders[] = $loader;
			return spl_autoload_register([$loader, 'load']);
		}
		
		return false;
	}
	
	/**
	 * Unregisters a class loader from the autoload stack.
	 * @param Loader $loader A loader to be unregistered.
	 */
	public function unregister(Loader $loader)
	{
		if(in_array($loader, $this->loaders)) {
			unset($this->loaders[array_search($loader, $this->loaders)]);
			spl_autoload_unregister([$loader, 'load']);
		}
	}
	
	/**
	 * Resets all registered loaders and unregister all loaders but the default
	 * one. This is used when only Zettacast's core classes are needed.
	 */
	public function reset()
	{
		foreach($this->loaders as $loader) {
			spl_autoload_unregister([$loader, 'load']);
			$loader->reset();
		}
		
		$this->register($this->framework);
	}
	
}
