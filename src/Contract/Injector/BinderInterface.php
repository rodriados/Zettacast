<?php
/**
 * Zettacast\Contract\Injector\BinderInterface interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Contract\Injector;

/**
 * This interface declares all needed methods for a properly working binder.
 * @package Zettacast\Injector
 */
interface BinderInterface
{
	/**
	 * Resolves a binding, or alias to its concrete implementation.
	 * @param string $abstract Requested abstraction name.
	 * @return object Resolved abstraction or false if not found.
	 */
	public function resolve(string $abstract);
	
	/**
	 * Checks whether given abstract is bound to a concrete implementation.
	 * @param string $abstract Abstraction to be checked.
	 * @return bool Is abstraction known to binder?
	 */
	public function knows(string $abstract): bool;
	
	/**
	 * Creates a new abstraction binding or aliasing, allowing for them to have
	 * their names enshortened or be instantiated.
	 * @param string $abstract Abstraction to be bound.
	 * @param string|\Closure $target Concrete object to abstraction.
	 * @param bool $shared Should abstraction be registered as a singleton?
	 */
	public function bind(string $abstract, $target, bool $shared = false);
	
	/**
	 * Deletes an abstraction binding.
	 * @param string $abstract Abstraction to be unbound.
	 */
	public function unbind(string $abstract);
	
}
