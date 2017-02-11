<?php
/**
 * Autoload\Loader\Base abstract class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Autoload\Loader;

/**
 * The Base abstract class exposes the methods a class need to have to
 * correctly implement a loader for Zettacast's autoload system.
 * @package Zettacast\Autoload
 */
abstract class Base {
	
	/**
	 * Base loader constructor. Initializes the class and set values to
	 * instance properties.
	 */
	public function __construct() {
		
		$this->reset();
		
	}

	/**
	 * Tries to load an invoked and not yet loaded class.
	 * @param string $class Class to be loaded.
	 * @return bool Was the class successfully loaded?
	 */
	abstract public function load(string $class) : bool;
	
	/**
	 * Resets the loader to its initial state.
	 */
	public function reset() {
	
		;
		
	}
	
}
