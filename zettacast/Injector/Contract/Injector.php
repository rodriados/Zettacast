<?php
/**
 * Zettacast\Injector\Contract\Injector interface class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Injector\Contract;

use Closure;

/**
 * This interface declares all needed methods for a properly working injector.
 * @package Zettacast\Injector
 */
interface Injector
{
	/**
	 * Creates a new object alias.
	 * @param string $abstract Abstraction to be aliased.
	 * @param string $alias Alias to be used for abstraction.
	 */
	public function alias(string $abstract, string $alias);
	
	/**
	 * Creates a new abstraction binding, allowing for them to be instantiated.
	 * @param string $abstract Abstraction to be bound.
	 * @param string|Closure $concrete Concrete object to abstraction.
	 * @param bool $shared Should abstraction become a singleton?
	 */
	public function bind(string $abstract, $concrete, bool $shared = false);
	
	/**
	 * Checks whether given abstract is bound to concrete implementation.
	 * @param string $abstract Abstraction to be checked.
	 * @return bool Is abstract bound?
	 */
	public function bound(string $abstract);
	
	/**
	 * Creates a new contextual binder instance.
	 * @param string $context Context to which binding is applied.
	 * @param string $abstract Abstraction to be bound.
	 * @param string|Closure $concrete Concrete object to abstraction.
	 */
	public function context(string $context, string $abstract, $concrete);
	
	/**
	 * Drops all data related to an abstraction.
	 * @param string $abstract Abstraction to be forgotten.
	 */
	public function drop(string $abstract);
	
	/**
	 * Creates a factory for given the abstraction.
	 * @param string $abstract Abstraction to be wrapped.
	 * @return Closure Factory for abstraction.
	 */
	public function factory(string $abstract) : Closure;
	
	/**
	 * Resolve the given abstraction and inject dependencies if needed.
	 * @param string $abstract Abstraction to be resolved.
	 * @return mixed Resolved abstraction.
	 */
	public function make(string $abstract);
	
	/**
	 * Gets the concrete type for a given abstraction.
	 * @param string $abstract Abstraction to be concretized.
	 * @param string $context Context to be resolved from.
	 * @return object Object containing concrete, context and shared data.
	 */
	public function resolve(string $abstract, string $context = null);
	
	/**
	 * Shares an existing instance to injector.
	 * @param string $abstract Abstraction to be shared.
	 * @param mixed $instance Shared instance to registered.
	 */
	public function share(string $abstract, $instance);
	
	/**
	 * Wraps a function and solves all of its dependencies.
	 * @param callable $fn Function to be wrapped.
	 * @param array $params Parameters to be used when invoked.
	 * @return Closure Wrapped function.
	 */
	public function wrap(callable $fn, array $params = []);
	
}
