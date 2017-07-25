<?php
/**
 * Zettacast\Autoload\Loader\Framework class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2016 Rodrigo Siqueira
 */
namespace Zettacast\Autoload\Loader;

use Zettacast\Contract\Autoload\Loader;

/**
 * The Framework loader class is responsible for loading all classes required
 * by the framework or the application itself. It also lets you set explicit
 * paths for classes to be loaded from.
 * @package Zettacast\Autoload
 * @version 1.0
 */
final class Framework
	implements Loader
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
	 * Autoload constructor.
	 * Initializes the class and set values to instance properties.
	 * @param string $fwork Framework files' path.
	 * @param string $app Application files' path.
	 */
	public function __construct(string $fwork, string $app)
	{
		$this->fwork = $fwork;
		$this->app = $app;
	}
	
	/**
	 * Tries to load an invoked and not yet loaded class. The lookup for
	 * classes happens in the framework core first and then it acts as PHP
	 * default autoloader.
	 * @param string $class Class to be loaded.
	 * @return bool Was the class successfully loaded?
	 */
	public function load(string $class) : bool
	{
		$name = explode('\\', ltrim($class, '\\'));
		$scope = array_shift($name);
		
		if($scope === \ZETTACAST)
			return $this->internal($name);
		
		if($scope === 'App')
			return $this->application($name);
		
		return false;
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
	 * search for files located on the framework's directory.
	 * @param array $name Class to be loaded exploded to qualified names.
	 * @return bool Was the class successfully loaded?
	 */
	protected function internal(array $name) : bool
	{
		array_unshift($name, $this->fwork);
		
		$cpath = implode('/', $name);
		$fname = $cpath.".php";
		
		if(!file_exists($fname))
			return false;
		
		require $fname;
		return true;
	}
	
	/**
	 * Tries to load an invoked and not yet loaded class. This method is a
	 * fallback for classes not located in the framework, so it searches in
	 * application directory.
	 * @param array $name Class to be loaded exploded to qualified names.
	 * @return bool Was the class successfully loaded?
	 */
	protected function application(array $name) : bool
	{
		$pkg = strtolower(array_shift($name));
		
		array_unshift($name, $pkg);
		array_unshift($name, $this->app);
		
		$cpath = implode('/', $name);
		$fname = $cpath.".php";
		
		if(!file_exists($fname))
			return false;
		
		require $fname;
		return true;
	}
	
}
