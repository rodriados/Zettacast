<?php
/**
 * Loader interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Autoload;

/**
 * The Loader interface exposes the methods a class need to have to correctly
 * implement a loader for Zettacast's autoload system.
 * @package Zettacast\Autoload
 */
interface Loader {
	
	/**
	 * Tries to load an invoked and not yet loaded class.
	 * @param string $class Class to be loaded.
	 * @return bool Was the class successfully loaded?
	 */
	public function load(string $class) : bool;
	
	/**
	 * Resets the loader to its initial state.
	 */
	public function reset();
	
}
