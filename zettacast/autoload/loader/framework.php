<?php
/**
 * Autoload\Loader\Framework class file.
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
final class Framework implements Loader {
	
	/**
	 * Tries to load an invoked and not yet loaded class. The lookup for
	 * classes happens in the framework core first and then it acts as PHP
	 * default autoloader.
	 * @param string $class Class to be loaded.
	 * @return bool Was the class successfully loaded?
	 */
	public function load(string $class) : bool {
		
		$class = ltrim($class, '\\');
		$elem = explode('\\', $class);
		
		if(array_shift($elem) != ZETTACAST)
			return $this->default($class);
		
		if(count($elem) == 1) /* façade */ {
			
			$lower = strtolower($elem[0]);
			$fname = FWORKPATH."/facade/{$lower}.php";
			
		} else /* internal framework use */ {
			
			$lower = strtolower(implode('/', $elem));
			$fname = FWORKPATH."/{$lower}.php";
			
		}

		if(!file_exists($fname))
			return false;
		
		require $fname;
		return true;
			
	}
	
	/**
	 * Tries to load an invoked and not yet loaded class. This method is a
	 * fallback for classes not located in the framework.
	 * @param string $class Class to be loaded.
	 * @return bool Was the class successfully loaded?
	 */
	private function default(string $class) {
		
		$lower = strtolower(str_replace('\\', '/', $class));
		$fname = DOCROOT."/{$lower}.php";
		
		if(!file_exists($fname))
			return false;
		
		require $fname;
		return true;
		
	}
	
	/**
	 * Resets the loader to its initial state.
	 * @return self Loader instance.
	 */
	public function reset() {
		
		return $this;
		
	}
	
}
