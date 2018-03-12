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
	 * Creates the internal framework loader and registers it.
	 */
	public function __construct()
	{
		$this->loaders = [];
		$this->internal = new InternalLoader;
		
		$this->register(spl_object_hash($this->internal), $this->internal);
	}
	
	/**
	 * Checks whether a loader has already been registered in stack.
	 * @param string $name Name of loader to check existance.
	 * @return bool Is loader already registered?
	 */
	public function has(string $name): bool
	{
		return isset($this->loaders[$name]);
	}
	
	/**
	 * Recovers access to a registered loader.
	 * @param string $name The name of loader to recover.
	 * @return LoaderInterface The recovered loader.
	 */
	public function get(string $name): ?LoaderInterface
	{
		return $this->loaders[$name] ?? null;
	}
	
	/**
	 * Registers a loader in autoload stack. The autoload function will be
	 * responsible for automatically loading all objects invoked by framework
	 * or by application.
	 * @param string $name The name to use for loader identification.
	 * @param LoaderInterface $loader A loader to register.
	 * @return bool Was the loader successfully registered?
	 */
	public function register(string $name, LoaderInterface $loader): bool
	{
		if($this->has($name))
			return false;
		
		$this->loaders[$name] = $loader;
		return spl_autoload_register([$loader, 'load']);
	}
	
	/**
	 * Unregisters a object loader from autoload stack.
	 * @param string $name Name of loader to unregister.
	 */
	public function unregister(string $name): void
	{
		if($this->has($name)) {
			spl_autoload_unregister([$this->loaders[$name], 'load']);
			unset($this->loaders[$name]);
		}
	}
	
	/**
	 * Registers a new alias.
	 * @param string $alias Aliased name to register.
	 * @param string $target Original name alias refers to.
	 */
	public function alias(string $alias, string $target): void
	{
		$this->internal->alias($alias, $target);
	}
	
	/**
	 * Removes an alias from map. Objects loaded using alias will not unload if
	 * they have already been loaded, but they will not be able to load using
	 * their aliased name anymore.
	 * @param string $alias Alias to remove.
	 */
	public function unalias(string $alias): void
	{
		$this->internal->unalias($alias);
	}
}
