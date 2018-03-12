<?php
/**
 * Zettacast\Injector\Binder class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
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
	protected $bindings;
	
	/**
	 * Binder constructor.
	 * This constructor simply sets all its properties to empty collections.
	 */
	public function __construct()
	{
		$this->bindings = new Collection;
	}
	
	/**
	 * Resolves a binding, or alias to its concrete implementation.
	 * @param string $abstract Requested abstraction name.
	 * @return array Resolved abstraction or null if not found.
	 */
	public function resolve(string $abstract): ?array
	{
		while(is_scalar($abstract) && $this->knows($abstract)) {
			$result = $this->bindings->get($abstract);
			$abstract = $result['concrete'];
		}
		
		return $result ?? null;
	}
	
	/**
	 * Checks whether given abstract is bound to a concrete implementation.
	 * @param string $abstract Abstraction to check.
	 * @return bool Is abstraction known to binder?
	 */
	public function knows(string $abstract): bool
	{
		return $this->bindings->has($abstract);
	}
	
	/**
	 * Creates a new abstraction binding or aliasing, allowing them to have
	 * their names enshortened or be instantiated.
	 * @param string $abstract Abstraction to bind.
	 * @param string|\Closure $target Concrete object to abstraction.
	 * @param bool $share Should abstraction register as a singleton?
	 */
	public function bind(string $abstract, $target, bool $share = false): void
	{
		$this->bindings->set($abstract, [
			'concrete' => $target,
			'shared'   => $share,
		]);
	}
	
	/**
	 * Deletes an abstraction binding.
	 * @param string $abstract Abstraction to unbind.
	 */
	public function unbind(string $abstract): void
	{
		$this->bindings->del($abstract);
	}
}
