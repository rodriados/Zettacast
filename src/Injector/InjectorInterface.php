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
 * @version 1.0
 */
interface InjectorInterface extends BinderInterface
{
	/**
	 * Creates a new object alias.
	 * @param string $alias Alias to be used for abstraction.
	 * @param string $abstract Abstraction to be aliased.
	 */
	public function alias(string $alias, string $abstract);
	
	/**
	 * Drops all data related to an abstraction.
	 * @param string $abstract Abstraction to be forgotten.
	 */
	public function drop(string $abstract);
	
	/**
	 * Creates a factory for the given abstraction.
	 * @param string $abstract Abstraction to be wrapped.
	 * @param array $outer Default parameters to be sent to object.
	 * @return \Closure Factory for abstraction.
	 */
	public function factory(string $abstract, array $outer = []): \Closure;
	
	/**
	 * Reverses a chain of alias and returns real name.
	 * @param string $alias Alias to be reversed.
	 * @return mixed Unaliased abstraction name.
	 */
	public function identify(string $alias);
	
	/**
	 * Resolve the given abstraction and inject dependencies if needed.
	 * @param string $abstract Abstraction to be resolved.
	 * @param array $params Parameters to be used when instantiating.
	 * @return mixed Resolved abstraction.
	 */
	public function make(string $abstract, array $params = []);
	
	/**
	 * Shares an existing instance to injector.
	 * @param string $abstract Abstraction to be resolved.
	 * @param mixed $instance Shared instance to registered.
	 */
	public function share(string $abstract, $instance);
	
	/**
	 * Unregisters an alias.
	 * @param string $alias Alias name to be unregistered.
	 */
	public function unalias(string $alias);
	
	/**
	 * Wraps a function and solves all of its dependencies.
	 * @param callable $fn Function to be wrapped.
	 * @param array $params Default parameters to be used when invoked.
	 * @return \Closure Wrapped function.
	 */
	public function wrap(callable $fn, array $params = []): \Closure;
	
}
