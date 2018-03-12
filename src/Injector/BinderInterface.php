<?php
/**
 * Zettacast\Injector\BinderInterface interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Injector;

interface BinderInterface
{
	/**
	 * Resolves a binding, or alias to its concrete implementation.
	 * @param string $abstract Requested abstraction name.
	 * @return array Resolved abstraction or null if not found.
	 */
	public function resolve(string $abstract): ?array;
	
	/**
	 * Checks whether given abstract is bound to a concrete implementation.
	 * @param string $abstract Abstraction to check.
	 * @return bool Is abstraction known to binder?
	 */
	public function knows(string $abstract): bool;
	
	/**
	 * Creates a new abstraction binding or aliasing, allowing them to have
	 * their names enshortened or be instantiated.
	 * @param string $abstract Abstraction to bind.
	 * @param string|\Closure $target Concrete object to abstraction.
	 * @param bool $share Should abstraction register as a singleton?
	 */
	public function bind(string $abstract, $target, bool $share = false): void;
	
	/**
	 * Deletes an abstraction binding.
	 * @param string $abstract Abstraction to unbind.
	 */
	public function unbind(string $abstract): void;
}
