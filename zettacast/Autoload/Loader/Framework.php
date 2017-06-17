<?php
/**
 * Zettacast\Autoload\Loader\Framework class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2016 Rodrigo Siqueira
 */
namespace Zettacast\Autoload\Loader;

use Zettacast\Autoload\Contract\Loader;

/**
 * The Framework loader class is responsible for loading all classes required
 * by the framework or the application itself. It also lets you set explicit
 * paths for classes to be loaded from.
 * @package Zettacast\Autoload
 * @version 1.0
 */
final class Framework implements Loader
{
	/**
	 * Framework's directory path.
	 * @var string Path to framework's files.
	 */
	protected $path;
	
	/**
	 * Autoload constructor.
	 * Initializes the class and set values to instance properties.
	 * @param string $path Framework's path.
	 */
	public function __construct(string $path = FWORKPATH)
	{
		$this->path = $path;
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
		$class = ltrim($class, '\\');
		$elem = explode('\\', $class);
		
		if($elem[0] != ZETTACAST)
			return $this->application($elem);

		array_shift($elem);
		$class = implode('/', $elem);
		$fname = $this->path."/{$class}.php";
		
		if(!file_exists($fname))
			return false;
		
		require $fname;
		return true;
	}
	
	/**
	 * Tries to load an invoked and not yet loaded class. This method is a
	 * fallback for classes not located in the framework, so it searches in
	 * application directory.
	 * @param array $class Class to be loaded exploded to namespaces.
	 * @return bool Was the class successfully loaded?
	 */
	private function application(array $class)
	{
		if(array_shift($class) != 'App')
			return false;
		
		$pkg = strtolower(array_shift($class));
		$cpath = implode('/', $class);
		$fname = APPPATH."/{$pkg}/{$cpath}.php";
		
		if(!file_exists($fname))
			return false;
		
		require $fname;
		return true;
	}
	
	/**
	 * Resets the loader to its initial state.
	 * @return self Loader instance.
	 */
	public function reset()
	{
		return $this;
	}
	
}
