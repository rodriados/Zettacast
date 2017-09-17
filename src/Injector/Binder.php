<?php
/**
 * Zettacast\Injector\Binder class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Injector;

use Zettacast\Collection\Collection;

/**
 * The binder class is responsible for linking abstractions to concrete
 * implementations.
 * @package Zettacast\Injector
 * @version 1.0
 */
class Binder implements BinderInterface
{
	/**
	 * Collection of abstraction bindings. Abstraction bindings allow the usage
	 * of contracts as the requested object, so the concrete object can be
	 * given by the injector.
	 * @var Collection Abstraction bindings collection.
	 */
	protected $links;
	
	/**
	 * Binder constructor. This constructor simply sets all properties to
	 * empty collections.
	 */
	public function __construct()
	{
		$this->links = new Collection;
	}
	
	/**
	 * Creates a new abstraction binding, allowing for them to be instantiated.
	 * @param string $abstract Abstraction to be bound.
	 * @param string|\Closure $concrete Concrete object to abstraction.
	 * @param bool $shared Should abstraction become a singleton?
	 * @return $this Binder for method chaining.
	 */
	public function bind(string $abstract, $concrete, bool $shared = false)
	{
		$this->links->set($abstract, (object)[
			'concrete' => $concrete,
			'shared' => $shared,
		]);
		
		return $this;
	}
	
	/**
	 * Checks whether given abstract is bound to concrete implementation.
	 * @param string $abstract Abstraction to be checked.
	 * @return bool Is abstract bound?
	 */
	public function isBound(string $abstract): bool
	{
		return $this->links->has($abstract);
	}
	
	/**
	 * Gets the concrete type for a given abstraction.
	 * @param string $abstract Abstraction to be concretized.
	 * @return mixed Object containing concrete and sharing info.
	 */
	public function resolve(string $abstract)
	{
		return $this->links->get($abstract);
	}
	
	/**
	 * Removes a link from an abstraction.
	 * @param string $abstract Abstraction to be unbound.
	 * @return $this Binder for method chaining.
	 */
	public function unbind(string $abstract)
	{
		$this->links->del($abstract);
		return $this;
	}
	
}
