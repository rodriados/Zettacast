<?php
/**
 * Zettacast\Injector\Injector class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Injector;

use Zettacast\Collection\Collection;
use Zettacast\Contract\StorageInterface;
use Zettacast\Injector\Binder\DefaultBinder;
use Zettacast\Injector\Binder\ContextualBinder;
use Zettacast\Contract\Injector\BinderInterface;
use Zettacast\Contract\Injector\InjectorInterface;

/**
 * The Injector class is responsible for handling dependency injection. This
 * works so complex objects, with their many dependencies can be much more
 * easily instantiated anywhere around the application.
 * @package Zettacast\Injector
 * @version 1.0
 */
class Injector implements InjectorInterface
{
	/**
	 * Collection of shared data. Objects or any kind of data can be shared in
	 * the injector. Shared objects will be instantiated only once.
	 * @var Collection Shared data collection.
	 */
	protected $shared;
	
	/**
	 * Abstraction binder instance. This object is responsible for allowing
	 * abstractions to be instantiated by linking them to a concrete
	 * implementation.
	 * @var BinderInterface Abstraction binder instance.
	 */
	protected $binder;
	
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
		$this->shared = $shared ?? new Collection;
		$this->binder = $binder ?? new DefaultBinder;
		$this->assembler = new Assembler($this);
	}
	
	/**
	 * Retrieves an instance shared with the injector.
	 * @param mixed $abstract Requested abstract object name.
	 * @return mixed Requested instance or null if not found.
	 */
	public function get($abstract)
	{
		return $this->shared->get($abstract);
	}
	
	/**
	 * Checks whether injector knows a shared instance.
	 * @param mixed $abstract Abstract object name to be checked.
	 * @return bool Is shared instance known to injector?
	 */
	public function has($abstract): bool
	{
		return $this->shared->has($abstract);
	}
	
	/**
	 * Shares an instance with the injector.
	 * @param mixed $abstract Abstract object name to be shared.
	 * @param mixed $instance The existing instance to be shared.
	 * @return $this Injector for method chaining.
	 */
	public function set($abstract, $instance)
	{
		$this->binder->unbind($abstract);
		$this->shared->set($abstract, $instance);
		return $this;
	}
	
	/**
	 * Deletes a shared instance from the injector.
	 * @param mixed $abstract Abstract object name to be deleted.
	 * @return $this Injector for method chaining.
	 */
	public function del($abstract)
	{
		$this->shared->del($abstract);
		return $this;
	}
	
	/**
	 * Retrieves an element bound in or shared to the injector.
	 * @param string $abstract Requested abstraction name.
	 * @return object Requested abstraction or null if not found.
	 */
	public function resolve(string $abstract)
	{
		return $this->binder->resolve($abstract);
	}
	
	/**
	 * Checks whether given abstract is bound to a concrete implementation.
	 * @param string $abstract Abstraction to be checked.
	 * @return bool Is abstraction known to binder?
	 */
	public function knows(string $abstract): bool
	{
		return $this->binder->knows($abstract);
	}
	
	/**
	 * Creates a new abstraction binding or aliasing, allowing for them to have
	 * their names enshortened or be instantiated.
	 * @param string $abstract Abstraction to be bound.
	 * @param string|\Closure $target Concrete object to abstraction.
	 * @param bool $shared Should abstraction be registered as a singleton?
	 * @return $this Injector for method chaining.
	 */
	public function bind(string $abstract, $target, bool $shared = false)
	{
		$this->binder->bind($abstract, $target, $shared);
		$this->shared->del($abstract);
		return $this;
	}
	
	/**
	 * Unbinds an abstraction, and drops all of its known instances.
	 * @param string $abstract Abstraction to be forgotten by the injector.
	 * @return $this Injector for method chaining.
	 */
	public function unbind(string $abstract)
	{
		$this->binder->unbind($abstract);
		return $this;
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
	 * Creates a factory for the given abstraction.
	 * @param string $abstract Abstraction to be wrapped.
	 * @param array $outer Default parameters to be sent to instance.
	 * @return \Closure Factory for abstraction.
	 */
	public function factory(string $abstract, array $outer = []): \Closure
	{
		return function(array $inner = []) use($abstract, $outer) {
			return $this->make($abstract, array_merge($inner, $outer));
		};
	}
	
	/**
	 * Resolve the given abstraction and inject its dependencies if needed.
	 * @param string $abstract Abstraction to be resolved.
	 * @param array $params Parameters to be used when instantiating.
	 * @return mixed Resolved abstraction.
	 */
	public function make(string $abstract, array $params = [])
	{
		return $this->assembler->make($abstract, $params);
	}
	
	/**
	 * Wraps a function and solves all of its dependencies.
	 * @param callable $fn Function to be wrapped.
	 * @param array $params Default parameters to be used when invoked.
	 * @return \Closure Wrapped function.
	 */
	public function wrap(callable $fn, array $params = []): \Closure
	{
		return $this->assembler->wrap($fn, $params);
	}
	
}
