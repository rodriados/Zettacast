<?php
/**
 * Zettacast\Autoload\Loader\Framework class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2016 Rodrigo Siqueira
 */
namespace Zettacast\Autoload\Loader;

use const ZETTACAST;
use Zettacast\Autoload\LoaderInterface;

/**
 * The Framework loader class is responsible for loading all classes required
 * by the framework or the application itself. It also lets you set explicit
 * paths for classes to be loaded from.
 * @package Zettacast\Autoload
 * @version 1.0
 */
final class Framework implements LoaderInterface
{
	/**
	 * Application's directory path.
	 * @var string Path to application's files.
	 */
	protected $app;
	
	/**
	 * Framework's directory path.
	 * @var string Path to framework's files.
	 */
	protected $fwork;
	
	/**
	 * Packages' directory path.
	 * @var string Path to packages' files.
	 */
	protected $pkg;
	
	/**
	 * Autoload constructor.
	 * Initializes the class and set values to instance properties.
	 * @param string $fwork Framework files' path.
	 * @param string $app Application files' path.
	 * @param string $pkg Package files' path.
	 */
	public function __construct(string $fwork, string $app, string $pkg)
	{
		$this->fwork = $fwork;
		$this->app = $app;
		$this->pkg = $pkg;
	}
	
	/**
	 * Tries to load an invoked and not yet loaded class. The lookup for
	 * classes happens in the framework core first and then it acts as PHP
	 * default autoloader.
	 * @param string $class Class to be loaded.
	 * @return bool Was the class successfully loaded?
	 */
	public function load(string $class): bool
	{
		$name = explode('\\', ltrim($class, '\\'));
		
		return $name[0] == ZETTACAST || $name == 'App'
			? $this->internal($name)
			: $this->package($name);
	}
	
	/**
	 * Resets the loader to its initial state.
	 * @return self Framework loader for method chaining.
	 */
	public function reset()
	{
		return $this;
	}
	
	/**
	 * Tries to load an invoked and not yet loaded class. This method will only
	 * search for files on the framework source and application directories.
	 * @param array $name Class to be loaded exploded to qualified names.
	 * @return bool Was the class successfully loaded?
	 */
	protected function internal(array $name): bool
	{
		$name[0] = ($name[0] == ZETTACAST)
			? $this->fwork
			: $this->app;
		
		$cpath = implode('/', $name);
		$fname = $cpath . '.php';
		
		if(!file_exists($fname))
			return false;
		
		require $fname;
		return true;
	}
	
	/**
	 * Tries to load an invoked and not yet loaded package class. This method
	 * is a fallback for classes not located within framework's directories, so
	 * it searches in package's directory.
	 * @param array $name Class to be loaded exploded to qualified names.
	 * @return bool Was the class successfully loaded?
	 */
	protected function package(array $name): bool
	{
		array_unshift($name, $this->pkg);
		$cpath = implode('/', $name);
		$fname = $cpath . '.php';
		
		if(!file_exists($fname))
			return false;
		
		require $fname;
		return true;
	}
	
}
