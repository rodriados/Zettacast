<?php
/**
 * Zettacast\Autoload\Autoload class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Autoload;

require FWORKPATH."/Contract/Autoload/LoaderInterface.php";
require FWORKPATH."/Autoload/Loader/FrameworkLoader.php";

use Zettacast\Autoload\Loader\FrameworkLoader;
use Zettacast\Contract\Autoload\LoaderInterface;

/**
 * The autoload class is responsible for loading all classes required by the
 * framework or the application itself. It also lets you set explicit paths for
 * classes to be loaded from.
 * @package Zettacast\Autoload
 * @version 1.0
 */
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
	 * @var FrameworkLoader Zettacast main loader instance.
	 */
	private $framework;
	
	/**
	 * Autoload constructor.
	 * Initializes the class and set values to instance properties.
	 * @param string $fwork Framework files' path.
	 * @param string $app Application files' path.
	 * @param string $rsrc Resources files' path.
	 */
	public function __construct(
		string $fwork = FWORKPATH,
		string $app = APPPATH,
		string $rsrc = RSRCPATH
	) {
		$this->loaders = [];
		$this->framework = new FrameworkLoader($fwork, $app, $rsrc);
		$this->register($this->framework);
	}
	
	/**
	 * Checks whether a loader has already been registered to the stack.
	 * @param LoaderInterface $loader Target to check whether registered.
	 * @return bool Is loader already registered?
	 */
	public function isRegistered(LoaderInterface $loader)
	{
		return isset($this->loaders[$hash = spl_object_hash($loader)])
			&& $this->loaders[$hash];
	}
	
	/**
	 * Registers a loader to the autoload stack. The autoload function will be
	 * the responsible for automatically loading all classes invoked by the
	 * framework or by the application.
	 * @var LoaderInterface $loader A loader to be registered.
	 * @return bool Was the loader successfully registered?
	 */
	public function register(LoaderInterface $loader)
	{
		if($this->isRegistered($loader)) {
			return false;
		}
		
		$this->loaders[spl_object_hash($loader)] = true;
		return spl_autoload_register([$loader, 'load']);
	}
	
	/**
	 * Unregisters a class loader from the autoload stack.
	 * @param LoaderInterface $loader A loader to be unregistered.
	 */
	public function unregister(LoaderInterface $loader)
	{
		if($this->isRegistered($loader)) {
			unset($this->loaders[spl_object_hash($loader)]);
			spl_autoload_unregister([$loader, 'load']);
		}
	}
	
}
