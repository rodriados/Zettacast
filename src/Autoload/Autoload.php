<?php
/**
 * Zettacast\Autoload\Autoload class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Autoload;

require FWORKPATH."/Contract/Autoload/Loader.php";
require FWORKPATH."/Autoload/Loader/Framework.php";

use Zettacast\Contract\Autoload\Loader;
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
	 * Stores the loaders already registered in the autoloading system. This
	 * allows us to keep track of all class loading functions.
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
	 * @param string $fwork Framework files' path.
	 * @param string $app Application files' path.
	 */
	public function __construct(
		string $fwork = FWORKPATH,
		string $app = APPPATH
	) {
		$this->loaders = [];
		$this->framework = new Framework($fwork, $app);
		$this->register($this->framework);
	}
	
	/**
	 * Checks whether a loader has already been registered to the stack.
	 * @param Loader $loader Target to check whether registered.
	 * @return bool Is loader already registered?
	 */
	public function inStack(Loader $loader)
	{
		return isset($this->loaders[$hash = spl_object_hash($loader)])
			and $this->loaders[$hash];
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
		if($this->inStack($loader)) {
			return false;
		}
		
		$this->loaders[spl_object_hash($loader)] = true;
		return spl_autoload_register([$loader, 'load']);
	}
	
	/**
	 * Unregisters a class loader from the autoload stack.
	 * @param Loader $loader A loader to be unregistered.
	 */
	public function unregister(Loader $loader)
	{
		if($this->inStack($loader)) {
			unset($this->loaders[spl_object_hash($loader)]);
			spl_autoload_unregister([$loader, 'load']);
		}
	}
	
}
