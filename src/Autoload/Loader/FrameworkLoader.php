<?php
/**
 * Zettacast\Autoload\Loader\FrameworkLoader class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2016 Rodrigo Siqueira
 */
namespace Zettacast\Autoload\Loader;

use const ZETTACAST;
use Zettacast\Contract\Autoload\LoaderInterface;

/**
 * The Framework loader class is responsible for loading all classes required
 * by the framework or the application itself. It also lets you set explicit
 * paths for classes to be loaded from.
 * @package Zettacast\Autoload
 * @version 1.0
 */
final class FrameworkLoader implements LoaderInterface
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
	 * Resources' directory path.
	 * @var string Path to resources' files.
	 */
	protected $rsrc;
	
	/**
	 * Autoload constructor.
	 * Initializes the class and set values to instance properties.
	 * @param string $fwork Framework files' path.
	 * @param string $app Application files' path.
	 * @param string $rsrc Resource files' path.
	 */
	public function __construct(string $fwork, string $app, string $rsrc)
	{
		$this->fwork = $fwork;
		$this->rsrc = $rsrc;
		$this->app = $app;
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
		
		return $name[0] == ZETTACAST || $name[0] == 'App'
			? $this->loadInternal($name)
			: $this->loadResource($name);
	}
	
	/**
	 * Tries to load an invoked and not yet loaded class. This method will only
	 * search for files on the framework source and application directories.
	 * @param array $name Class to be loaded exploded to qualified names.
	 * @return bool Was the class successfully loaded?
	 */
	protected function loadInternal(array $name): bool
	{
		$nspace = array_shift($name);
		$cpath = implode('/', $name);
		
		$fname = $nspace == ZETTACAST
			? $this->fwork.'/'.$cpath.'.php'
			: $this->app.'/'.strtolower($cpath).'.php';
		
		if($loaded = file_exists($fname))
			require $fname;
		
		return $loaded;
	}
	
	/**
	 * Tries to load an invoked and not yet loaded resource class. This method
	 * is a fallback for classes not located within framework's directories, so
	 * it searches in resources' directory.
	 * @param array $name Class to be loaded exploded to qualified names.
	 * @return bool Was the class successfully loaded?
	 */
	protected function loadResource(array $name): bool
	{
		$pkg = array_shift($name);
		$cpath = implode('/', $name);
		$fname = $this->rsrc."/{$pkg}/src/{$cpath}.php";
		
		if($loaded = file_exists($fname))
			require $fname;
		
		return $loaded;
	}
	
}
