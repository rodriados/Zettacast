<?php
/**
 * Zettacast\Injector\BinderInterface interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Injector;

/**
 * This interface declares all needed methods for a properly working binder.
 * @package Zettacast\Injector
 */
interface BinderInterface
{
	/**
	 * Creates a new abstraction binding, allowing for them to be instantiated.
	 * @param string $abstract Abstraction to be bound.
	 * @param string|\Closure $concrete Concrete object to abstraction.
	 * @param bool $shared Should abstraction become a singleton?
	 */
	public function bind(string $abstract, $concrete, bool $shared = false);
	
	/**
	 * Checks whether given abstract is bound to concrete implementation.
	 * @param string $abstract Abstraction to be checked.
	 * @return bool Is abstract bound?
	 */
	public function isBound(string $abstract): bool;
	
	/**
	 * Gets the concrete type for a given abstraction.
	 * @param string $abstract Abstraction to be concretized.
	 * @return mixed Object containing concrete and sharing info.
	 */
	public function resolve(string $abstract);
	
	/**
	 * Removes a link from an abstraction.
	 * @param string $abstract Abstraction to be unbound.
	 */
	public function unbind(string $abstract);
	
}
