<?php
/**
 * Zettacast\Injector\Binder\DefaultBinder class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Injector\Binder;

use Zettacast\Collection\Collection;
use Zettacast\Contract\Injector\BinderInterface;

/**
 * The binder class is responsible for linking abstractions to concrete
 * implementations.
 * @package Zettacast\Injector
 * @version 1.0
 */
class DefaultBinder implements BinderInterface
{
	/**
	 * Collection of abstraction bindings. Abstraction bindings allow the usage
	 * of contracts as the requested object, so the concrete object can be
	 * given by the injector.
	 * @var Collection Abstraction bindings collection.
	 */
	protected $bindings;
	
	/**
	 * Binder constructor. This constructor simply sets all properties to
	 * empty collections.
	 */
	public function __construct()
	{
		$this->bindings = new Collection;
	}
	
	/**
	 * Resolves a binding, or alias to its concrete shape.
	 * @param string $abstract Requested abstraction name.
	 * @return object Resolved abstraction or false if not found.
	 */
	public function resolve(string $abstract)
	{
		while(is_scalar($abstract) && $this->knows($abstract)) {
			$result = $this->bindings->get($abstract);
			$abstract = $result->concrete;
		}

		return $result ?? null;
	}
	
	/**
	 * Checks whether given abstract is bound to a concrete implementation.
	 * @param string $abstract Abstraction to be checked.
	 * @return bool Is abstraction known to binder?
	 */
	public function knows(string $abstract): bool
	{
		return $this->bindings->has($abstract);
	}
	
	/**
	 * Creates a new abstraction binding or aliasing, allowing for them to have
	 * their names enshortened or be instantiated.
	 * @param string $abstract Abstraction to be bound.
	 * @param string|\Closure $concrete Concrete object to abstraction.
	 * @param bool $shared Should abstraction be registered as a singleton?
	 * @return $this Binder for method chaining.
	 */
	public function bind(string $abstract, $concrete, bool $shared = false)
	{
		$this->bindings->set($abstract, (object)[
			'concrete' => $concrete,
			'shared'   => $shared,
		]);
		
		return $this;
	}
	
	/**
	 * Deletes an abstraction binding.
	 * @param string $abstract Abstraction to be unbound.
	 * @return $this Binder for method chaining.
	 */
	public function unbind(string $abstract)
	{
		$this->bindings->del($abstract);
		return $this;
	}
	
}
