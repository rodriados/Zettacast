<?php
/**
 * Zettacast\Injector\Injector class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Injector;

use Closure;
use Zettacast\Helper\Aliaser;
use Zettacast\Collection\Collection;
use Zettacast\Injector\Binder\Binder;
use Zettacast\Injector\Binder\Scoped;
use Zettacast\Contract\Injector\Binder as BinderContract;
use Zettacast\Contract\Injector\Injector as InjectorContract;

/**
 * The Injector class is responsible for handling dependency injection. This
 * works so complex objects, with their many dependencies can be much more
 * easily instantiated anywhere around the application.
 * @package Zettacast\Injector
 * @version 1.0
 */
class Injector
	implements InjectorContract, BinderContract
{
	/**
	 * Collection of object aliases. Aliases allow full class names to be
	 * shortened to a more readable and easier name.
	 * @var Aliaser Object aliases collection.
	 */
	protected $alias;
	
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
	 * @var Binder Abstraction binder instance.
	 */
	protected $binder;
	
	/**
	 * Object builder instance. This object is responsible for instantiating
	 * and injecting all dependencies an instance might have.
	 * @var Builder Object builder instance.
	 */
	protected $builder;
	
	/**
	 * Injector constructor. This constructor simply sets all properties to
	 * empty collections. Each of these collections have a special use.
	 */
	public function __construct()
	{
		$this->alias = new Aliaser;
		$this->binder = new Binder;
		$this->shared = new Collection;
		$this->builder = new Builder($this, $this->shared);
	}
	
	/**
	 * Creates a new object alias.
	 * @param string $alias Alias to be used for abstraction.
	 * @param string $abstract Abstraction to be aliased.
	 * @return static Injector for method chaining.
	 */
	public function alias(string $alias, string $abstract)
	{
		$this->drop($alias);
		$this->alias->register($alias, $abstract);
		return $this;
	}
	
	/**
	 * Creates a new abstraction binding, allowing for them to be instantiated.
	 * @param string $abstract Abstraction to be bound.
	 * @param string|Closure $concrete Concrete object to abstraction.
	 * @param bool $shared Should abstraction become a singleton?
	 * @return static Injector for method chaining.
	 */
	public function bind(string $abstract, $concrete, bool $shared = false)
	{
		$this->drop($abstract);
		$this->binder->bind(
			$this->alias->identify($abstract),
			$concrete,
			$shared
		);
		
		return $this;
	}
	
	/**
	 * Checks whether given abstract is bound to concrete implementation.
	 * @param string $abstract Abstraction to be checked.
	 * @return bool Is abstract bound?
	 */
	public function bound(string $abstract) : bool
	{
		return $this->binder->bound($this->alias->identify($abstract));
	}
	
	/**
	 * Drops all data related to an abstraction.
	 * @param string $abstract Abstraction to be forgotten.
	 * @return static Injector for method chaining.
	 */
	public function drop(string $abstract)
	{
		$this->shared->remove($abstract);
		$this->binder->unbind($abstract);
		$this->alias->unregister($abstract);
		return $this;
	}
	
	/**
	 * Creates a factory for given the abstraction.
	 * @param string $abstract Abstraction to be wrapped.
	 * @param array $outer Default parameters to be sent to object.
	 * @return Closure Factory for abstraction.
	 */
	public function factory(string $abstract, array $outer = []) : Closure
	{
		return function(array $inner = []) use($abstract, $outer) {
			return $this->builder->init()->make(
				$abstract,
				array_merge($outer, $inner)
			);
		};
	}
	
	/**
	 * Reverses a chain of alias and returns real name.
	 * @param string $alias Alias to be reversed.
	 * @return mixed Real name.
	 */
	public function identify(string $alias)
	{
		return $this->alias->identify($alias);
	}
	
	/**
	 * Resolve the given abstraction and inject dependencies if needed.
	 * @param string $abstract Abstraction to be resolved.
	 * @param array $params Parameters to be used when instantiating.
	 * @return mixed Resolved abstraction.
	 */
	public function make(string $abstract, array $params = [])
	{
		return $this->builder->init()->make(
			$abstract,
			$params
		);
	}
	
	/**
	 * Gets the concrete type for a given abstraction.
	 * @param string $abstract Abstraction to be concretized.
	 * @return object|null Object containing concrete and sharing info.
	 */
	public function resolve(string $abstract)
	{
		return $this->binder->resolve($this->identify($abstract));
	}
	
	/**
	 * Shares an existing instance to injector.
	 * @param string $abstract Abstraction to be resolved.
	 * @param mixed $instance Shared instance to registered.
	 * @return static Injector for method chaining.
	 */
	public function share(string $abstract, $instance)
	{
		$this->alias->unregister($abstract);
		$this->shared->set($abstract, $instance);
		return $this;
	}
	
	/**
	 * Unregisters an alias.
	 * @param string $alias Alias name to be unregistered.
	 * @return static Injector for method chaining.
	 */
	public function unalias(string $alias)
	{
		$this->alias->unregister($alias);
		return $this;
	}
	
	/**
	 * Removes an abstraction binding.
	 * @param string $abstract Abstraction to be unbound.
	 * @return static Injector for method chaining.
	 */
	public function unbind(string $abstract)
	{
		$this->binder->unbind($abstract);
		return $this;
	}
	
	/**
	 * Creates a new scoped binder instance.
	 * @param string $scope Creation scope to which binding is applied.
	 * @return Scoped Scoped binder instance.
	 */
	public function when(string $scope) : Scoped
	{
		return new Scoped($scope, $this->binder);
	}
	
	/**
	 * Wraps a function and solves all of its dependencies.
	 * @param callable $fn Function to be wrapped.
	 * @param array $params Default parameters to be used when invoked.
	 * @return Closure Wrapped function.
	 */
	public function wrap(callable $fn, array $params = []) : Closure
	{
		return $this->builder->init()->wrap($fn, $params);
	}
	
}
