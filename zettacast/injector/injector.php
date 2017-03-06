<?php
/**
 * Zettacast\Injector\Injector class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast;

use Closure;
use Zettacast\Injector\Binder;
use Zettacast\Injector\Builder;
use Zettacast\Collection\Basic as Collection;
use Zettacast\Injector\Contract\Injector as InjectorContract;

/**
 * The injector class is responsible for handling dependency injection. This
 * works so complex objects, with their many dependencies can be much more
 * easily instantiated anywhere around the application.
 * @package Zettacast\Injector
 * @version 1.0
 */
class Injector implements InjectorContract {
	
	/**
	 * Collection of object aliases. Aliases allow full class names to be
	 * shortened to a more readable and easier name.
	 * @var Collection Object aliases collection.
	 */
	protected $aliases;
	
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
	 * Collection of shared data. Objects or any kind of data can be shared in
	 * the injector. Shared objects will be instantiated only once.
	 * @var Collection Shared data collection.
	 */
	protected $shared;
	
	/**
	 * Injector constructor. This constructor simply sets all properties to
	 * empty collections. Each of these collections have a special use.
	 */
	public function __construct() {
		
		$this->aliases  = new Collection;
		$this->shared   = new Collection;
		
		$this->binder   = new Binder;
		$this->builder  = new Builder($this);
		
	}
	
	/**
	 * Creates a new object alias.
	 * @param string $abstract Abstraction to be aliased.
	 * @param string $alias Alias to be used for abstraction.
	 */
	public function alias(string $abstract, string $alias) {
		
		if($abstract == $alias)
			return;
		
		$this->drop($alias);
		$this->aliases->set($alias, $abstract);
		
	}
	
	/**
	 * Creates a new abstraction binding, allowing for them to be instantiated.
	 * @param string $abstract Abstraction to be bound.
	 * @param string|Closure $concrete Concrete object to abstraction.
	 * @param bool $shared Should abstraction become a singleton?
	 */
	public function bind(string $abstract, $concrete, bool $shared = false) {
		
		$this->drop($abstract);
		
		$abstract = $this->identify($abstract);
		$this->binder->bind($abstract, $concrete, $shared);
		
	}
	
	/**
	 * Checks whether given abstract is bound to concrete implementation.
	 * @param string $abstract Abstraction to be checked.
	 * @return bool Is abstract bound?
	 */
	public function bound(string $abstract) {
		
		$abstract = $this->identify($abstract);
		return $this->binder->bound($abstract);
		
	}
	
	/**
	 * Creates a new contextual binder instance.
	 * @param string $context Context to which binding is applied.
	 * @param string $abstract Abstraction to be bound.
	 * @param string|Closure $concrete Concrete object to abstraction.
	 */
	public function context(string $context, string $abstract, $concrete) {
		
		$abstract = $this->identify($abstract);
		$this->binder->bind($abstract, $concrete, false, $context);
		
	}
	
	/**
	 * Drops all data related to an abstraction.
	 * @param string $abstract Abstraction to be forgotten.
	 */
	public function drop(string $abstract) {
		
		$this->aliases->del($abstract);
		$this->shared->del($abstract);
		$this->binder->unbind($abstract);
		
	}
	
	/**
	 * Creates a factory for given the abstraction.
	 * @param string $abstract Abstraction to be wrapped.
	 * @return Closure Factory for abstraction.
	 */
	public function factory(string $abstract) : Closure {
		
		return function() use($abstract) {
			return $this->make($abstract);
		};
		
	}
	
	/**
	 * Resolve the given abstraction and inject dependencies if needed.
	 * @param string $abstract Abstraction to be resolved.
	 * @return mixed Resolved abstraction.
	 */
	public function make(string $abstract) {
		
		$abstract = $this->identify($abstract);
		
		$link = $this->resolve($abstract);
		$concrete = $link->concrete ?: $abstract;
		
		if(!$link->context and $this->shared->has($abstract))
			return $this->shared->get($abstract);
		
		$object = !$concrete instanceof Closure ? $abstract === $concrete
			? $this->builder->make($concrete)
			: $this->make($concrete)
			: $concrete($this);
		
		if(!$link->context and $link->shared)
			$this->share($abstract, $object);
		
		return $object;
		
	}
	
	/**
	 * Gets the concrete type for a given abstraction.
	 * @param string $abstract Abstraction to be concretized.
	 * @param string $context Context to be resolved from.
	 * @return object Object containing concrete, context and shared data.
	 */
	public function resolve(string $abstract, string $context = null) {
		
		return $this->binder->resolve($abstract, $context);
		
	}
	
	/**
	 * Shares an existing instance to injector.
	 * @param string $abstract Abstraction to be shared.
	 * @param mixed $instance Shared instance to registered.
	 */
	public function share(string $abstract, $instance) {
		
		$this->aliases->del($abstract);
		$this->shared->set($abstract, $instance);
		
	}
	
	/**
	 * Wraps a function and solves all of its dependencies.
	 * @param callable $fn Function to be wrapped.
	 * @param array $params Parameters to be used when invoked.
	 * @return Closure Wrapped function.
	 */
	public function wrap(callable $fn, array $params = []) {
		
		return $this->builder->wrap($fn, $params);
		
	}
	
	/**
	 * Reverses a chain of alias and returns real type name.
	 * @param string $alias Alias to be reversed.
	 * @return mixed Real type name.
	 */
	protected function identify(string $alias) {
		
		return $this->aliases->has($alias)
			? $this->identify($this->aliases->get($alias))
			: $alias;
		
	}
	
}
