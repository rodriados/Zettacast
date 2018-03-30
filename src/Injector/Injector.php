<?php
/**
 * Zettacast\Injector\Injector class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Injector;

use Zettacast\Collection\Collection;
use Zettacast\Support\StorageInterface;

/**
 * The injector class is responsible for handling dependency injection. This
 * works so complex objects, with their many dependencies, can be much more
 * easily instantiated anywhere around the application.
 * @package Zettacast\Injector
 * @version 1.0
 */
class Injector implements InjectorInterface
{
	/**
	 * Abstraction binder instance. This object is responsible for allowing
	 * abstractions to be instantiated by linking them to a concrete
	 * implementation.
	 * @var BinderInterface Abstraction binder instance.
	 */
	protected $binder;
	
	/**
	 * Collection of shared data. Objects or any kind of data can be shared in
	 * the injector. Shared objects will be instantiated only once.
	 * @var Collection Shared data collection.
	 */
	protected $shared;
	
	/**
	 * Object assembler instance. This object is responsible for instantiating,
	 * assembling and injecting all dependencies an object instance might have.
	 * @var Assembler Object assembler instance.
	 */
	protected $assembler;
	
	/**
	 * Injector constructor.
	 * This constructor simply instantiates all properties.
	 * @param BinderInterface $binder Abstraction binder to guide injections.
	 * @param StorageInterface $shared Storage of shared instances.
	 */
	public function __construct(
		BinderInterface $binder = null,
		StorageInterface $shared = null
	) {
		$this->binder = $binder ?? new Binder;
		$this->shared = $shared ?? new Collection;
		$this->assembler = new Assembler($this);
	}
	
	/**
	 * Retrieves an instance shared with injector.
	 * @param mixed $abstract Requested abstract object name.
	 * @return mixed Requested instance or null if not found.
	 */
	public function get($abstract)
	{
		return $this->shared->get($abstract);
	}
	
	/**
	 * Checks whether injector knows a shared instance.
	 * @param mixed $abstract Abstract object name to check existance.
	 * @return bool Is shared instance known to injector?
	 */
	public function has($abstract): bool
	{
		return $this->shared->has($abstract);
	}
	
	/**
	 * Shares an instance with injector.
	 * @param mixed $abstract Abstract object name to share.
	 * @param mixed $instance The existing instance to share.
	 */
	public function set($abstract, $instance): void
	{
		$this->binder->unbind($abstract);
		$this->shared->set($abstract, $instance);
	}
	
	/**
	 * Deletes a shared instance from injector.
	 * @param mixed $abstract Abstract object name to delete.
	 */
	public function del($abstract): void
	{
		$this->shared->del($abstract);
	}
	
	/**
	 * Retrieves an element bound in or shared to injector.
	 * @param string $abstract Requested abstraction name.
	 * @return array Requested abstraction or null if not found.
	 */
	public function resolve(string $abstract): ?array
	{
		return $this->binder->resolve($abstract);
	}
	
	/**
	 * Checks whether given abstract is bound to a concrete implementation.
	 * @param string $abstract Abstraction to check.
	 * @return bool Is abstraction known to binder?
	 */
	public function knows(string $abstract): bool
	{
		return $this->binder->knows($abstract);
	}
	
	/**
	 * Creates a new abstraction binding or aliasing, allowing for them to have
	 * their names enshortened or be instantiated.
	 * @param string $abstract Abstraction to bind.
	 * @param string|\Closure $target Concrete object to abstraction.
	 * @param bool $shared Should abstraction register as a singleton?
	 */
	public function bind(string $abstract, $target, bool $shared = false): void
	{
		$this->binder->bind($abstract, $target, $shared);
		$this->shared->del($abstract);
	}
	
	/**
	 * Unbinds an abstraction, and drops all of its known instances.
	 * @param string $abstract Abstraction for injector to forget.
	 */
	public function unbind(string $abstract): void
	{
		$this->binder->unbind($abstract);
	}
	
	/**
	 * Drops any instance or binding related to given abstraction.
	 * @param string $abstract Abstraction to be dropped from injector.
	 */
	public function drop(string $abstract): void
	{
		$this->shared->del($abstract);
		$this->binder->unbind($abstract);
	}
	
	/**
	 * Informs the context to which binding operations should act upon.
	 * @param string $scope Creation scope to which binding is applied.
	 * @return BinderInterface Binder responsible for the given context.
	 */
	public function when(string $scope): BinderInterface
	{
		return new ContextualBinder($scope, $this->binder);
	}
	
	/**
	 * Creates a factory for given abstraction.
	 * @param string $abstract Abstraction to wrap.
	 * @param array $outer Default parameters to send to instance.
	 * @return \Closure Factory for abstraction.
	 */
	public function factory(string $abstract, array $outer = []): \Closure
	{
		return function(array $inner = []) use($abstract, $outer) {
			return $this->make($abstract, array_merge($inner, $outer));
		};
	}
	
	/**
	 * Resolves given abstraction and injects its dependencies if needed.
	 * @param string $abstract Abstraction to resolve.
	 * @param array $params Parameters to be used when instantiating.
	 * @return mixed Resolved abstraction.
	 */
	public function make(string $abstract, array $params = [])
	{
		return $this->assembler->make($abstract, $params);
	}
	
	/**
	 * Calls a function after solving all of its dependencies.
	 * @param callable $fn Function to call.
	 * @param array $param Parameters to use when calling.
	 * @return mixed The function return value.
	 */
	public function call(callable $fn, array $param = [])
	{
		$wrapped = $this->wrap($fn, $param);
		return $wrapped();
	}
	
	/**
	 * Wraps a function and solves all of its dependencies.
	 * @param callable $fn Function to wrap.
	 * @param array $params Default parameters to use when invoked.
	 * @return \Closure Wrapped function.
	 */
	public function wrap(callable $fn, array $params = []): \Closure
	{
		return $this->assembler->wrap($fn, $params);
	}
}
