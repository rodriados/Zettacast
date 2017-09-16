<?php
/**
 * Zettacast\Injector\InjectorInterface interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Injector;

/**
 * This interface declares all needed methods for a properly working injector.
 * @package Zettacast\Injector
 */
interface InjectorInterface
{
	/**
	 * Creates a factory for the given abstraction.
	 * @param string $abstract Abstraction to be wrapped.
	 * @param array $outer Default parameters to be sent to object.
	 * @return \Closure Factory for abstraction.
	 */
	public function factory(string $abstract, array $outer = []): \Closure;
	
	/**
	 * Resolve the given abstraction and inject dependencies if needed.
	 * @param string $abstract Abstraction to be resolved.
	 * @param array $params Parameters to be used when instantiating.
	 * @return mixed Resolved abstraction.
	 */
	public function make(string $abstract, array $params = []);
	
	/**
	 * Wraps a function and solves all of its dependencies.
	 * @param callable $fn Function to be wrapped.
	 * @param array $params Default parameters to be used when invoked.
	 * @return \Closure Wrapped function.
	 */
	public function wrap(callable $fn, array $params = []): \Closure;
	
}
