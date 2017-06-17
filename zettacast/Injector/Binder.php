<?php
/**
 * Zettacast\Injector\Binder class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Injector;

use Zettacast\Collection\Basic as Collection;

/**
 * The binder class is responsible for linking abstractions to concrete
 * implementations. This class also allows the creation of contextual bindings
 * so an abstraction can resolve to different objects depending on a context.
 * @package Zettacast\Injector
 * @version 1.0
 */
class Binder
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
	 * empty collections. Each of these collections have a special use.
	 */
	public function __construct()
	{
		$this->links    = new Collection;
	}
	
	/**
	 * Creates a new abstraction binding, allowing for them to be instantiated.
	 * @param string $abstract Abstraction to be bound.
	 * @param string|\Closure $concrete Concrete object to abstraction.
	 * @param bool $shared Should abstraction become a singleton?
	 * @param string $context Context to which binding is applied.
	 */
	public function bind (
		string $abstract,
		$concrete,
		bool $shared = false,
		string $context = null
	) {
		$this->links->set($context.$abstract, (object)[
			'concrete'  => $concrete,
			'context'   => (bool)$context,
			'shared'    => $shared,
		]);
	}
	
	/**
	 * Checks whether given abstract is bound to concrete implementation.
	 * @param string $abstract Abstraction to be checked.
	 * @param string $context Context to be checked from.
	 * @return bool Is abstract bound?
	 */
	public function bound(string $abstract, string $context = null)
	{
		return !is_null($context) and $this->links->has($context.$abstract)
			or $this->links->has($abstract);
	}
	
	/**
	 * Gets the concrete type for a given abstraction.
	 * @param string $abstract Abstraction to be concretized.
	 * @param string $context Context to be resolved from.
	 * @return object Object containing concrete, context and shared data.
	 */
	public function resolve(string $abstract, string $context = null)
	{
		if($context && $result = $this->links->get($context.$abstract))
			return $result;
		
		if($result = $this->links->get($abstract))
			return $result;
		
		return (object)[
			'concrete'  => null,
			'context'   => false,
			'shared'    => false,
		];
	}
	
	/**
	 * Removes a link from an abstraction.
	 * @param string $abstract Abstraction to be unbound.
	 * @param string|null $context Applied context to binding.
	 */
	public function unbind(string $abstract, string $context = null)
	{
		$this->links->del($context.$abstract);
	}
	
}
