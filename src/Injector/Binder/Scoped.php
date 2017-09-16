<?php
/**
 * Zettacast\Injector\Binder\Scoped class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Injector\Binder;

use Zettacast\Injector\BinderInterface;

/**
 * The Scoped binder class is responsible for linking abstractions to different
 * concrete implementations in different creation scopes. This class acts as
 * a parasite to other binders, injecting its information among their data and
 * retrieving it from them when needed.
 * @package Zettacast\Injector
 * @version 1.0
 */
class Scoped implements BinderInterface
{
	/**
	 * Abstraction binder. This binder will be manipulated so all scoped
	 * bindings are stored in the same place as normal bindings, just in a
	 * slightly different way.
	 * @var BinderInterface Abstraction binder object.
	 */
	protected $binder;
	
	/**
	 * Creation scope to which abstractions will be bound to.
	 * @var string Target binding creation scope.
	 */
	protected $scope;
	
	/**
	 * Scoped binder constructor. This constructor receives a real binder
	 * to which scoped bindings will be stored.
	 * @param string $scope Creation scope to which abstractions will be bound.
	 * @param BinderInterface $binder Host binder to scoped bindings.
	 */
	public function __construct(string $scope, BinderInterface $binder)
	{
		$this->scope = $scope;
		$this->binder = $binder;
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
		$this->binder->bind($abstract.'@'.$this->scope, $concrete, $shared);
		return $this;
	}
	
	/**
	 * Checks whether given abstract is bound to concrete implementation.
	 * @param string $abstract Abstraction to be checked.
	 * @return bool Is abstract bound?
	 */
	public function bound(string $abstract): bool
	{
		return $this->binder->bound($abstract.'@'.$this->scope);
	}
	
	/**
	 * Gets the concrete type for a given abstraction.
	 * @param string $abstract Abstraction to be concretized.
	 * @return mixed Object containing concrete and sharing info.
	 */
	public function resolve(string $abstract)
	{
		return $this->binder->resolve($abstract.'@'.$this->scope);
	}
	
	/**
	 * Removes a link from an abstraction.
	 * @param string $abstract Abstraction to be unbound.
	 * @return $this Binder for method chaining.
	 */
	public function unbind(string $abstract)
	{
		$this->binder->unbind($abstract.'@'.$this->scope);
		return $this;
	}
	
}
