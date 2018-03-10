<?php
/**
 * Zettacast\Injector\InjectorInterface interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Injector;

use Zettacast\Helper\StorageInterface;

interface InjectorInterface extends StorageInterface, BinderInterface
{
	/**
	 * Create a factory for given abstraction.
	 * @param string $abstract Abstraction to wrap.
	 * @param array $params Default parameters to send to object.
	 * @return \Closure Factory for abstraction.
	 */
	public function factory(string $abstract, array $params = []): \Closure;
	
	/**
	 * Resolve given abstraction and inject dependencies if needed.
	 * @param string $abstract Abstraction to resolve.
	 * @param array $params Parameters to use when instantiating.
	 * @return mixed Resolved abstraction.
	 */
	public function make(string $abstract, array $params = []);
	
	/**
	 * Inform the building context to which binding operations are related to.
	 * @param string $scope Creation scope to which binding is applied.
	 * @return BinderInterface Binder responsible for given context.
	 */
	public function when(string $scope): BinderInterface;
	
	/**
	 * Wrap a function and solves all of its dependencies.
	 * @param callable $fn Function to wrap.
	 * @param array $params Default parameters to use when invoked.
	 * @return \Closure Wrapped function.
	 */
	public function wrap(callable $fn, array $params = []): \Closure;
}
