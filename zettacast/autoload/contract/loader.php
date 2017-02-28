<?php
/**
 * Autoload\Contract\Loader interface class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Autoload\Contract;

/**
 * The Loader interface is responsible for exposing the common methods of
 * loaders used in the autoload package.
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
	 * @return null No return value required.
	 */
	public function reset();
	
}
