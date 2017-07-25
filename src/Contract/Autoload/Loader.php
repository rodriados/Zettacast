<?php
/**
 * Zettacast\Contract\Autoload\Loader interface class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Contract\Autoload;

/**
 * The Loader interface is responsible for exposing the common methods of
 * loaders used in the autoload package.
 * @package Zettacast\Contract\Autoload
 */
interface Loader
{
	/**
	 * Tries to load an invoked and not yet loaded class.
	 * @param string $class Class to be loaded.
	 * @return bool Was the class successfully loaded?
	 */
	public function load(string $class) : bool;
	
	/**
	 * Resets the loader to its initial state.
	 * @return static Loader for method chaining.
	 */
	public function reset();
	
}
