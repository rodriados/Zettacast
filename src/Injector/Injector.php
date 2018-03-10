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
use Zettacast\Helper\StorageInterface;

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
		$this->binder = $binder ?? new Binder;
		$this->assembler = new Assembler($this);
	}
	
	/**
	 * @inheritdoc
	 */
	public function get($abstract)
	{
		return $this->shared->get($abstract);
	}
	
	/**
	 * @inheritdoc
	 */
	public function has($abstract): bool
	{
		return $this->shared->has($abstract);
	}
	
	/**
	 * @inheritdoc
	 */
	public function set($abstract, $instance): void
	{
		$this->binder->unbind($abstract);
		$this->shared->set($abstract, $instance);
	}
	
	/**
	 * @inheritdoc
	 */
	public function del($abstract): void
	{
		$this->shared->del($abstract);
	}
	
	/**
	 * @inheritdoc
	 */
	public function resolve(string $abstract)
	{
		return $this->binder->resolve($abstract);
	}
	
	/**
	 * @inheritdoc
	 */
	public function knows(string $abstract): bool
	{
		return $this->binder->knows($abstract);
	}
	
	/**
	 * @inheritdoc
	 */
	public function bind(string $abstract, $target, bool $shared = false): void
	{
		$this->binder->bind($abstract, $target, $shared);
		$this->shared->del($abstract);
	}
	
	/**
	 * @inheritdoc
	 */
	public function unbind(string $abstract): void
	{
		$this->binder->unbind($abstract);
	}
	
	/**
	 * @inheritdoc
	 */
	public function when(string $scope): BinderInterface
	{
		return new ContextualBinder($scope, $this->binder);
	}
	
	/**
	 * @inheritdoc
	 */
	public function factory(string $abstract, array $outer = []): \Closure
	{
		return function(array $inner = []) use($abstract, $outer) {
			return $this->make($abstract, array_merge($inner, $outer));
		};
	}
	
	/**
	 * @inheritdoc
	 */
	public function make(string $abstract, array $params = [])
	{
		return $this->assembler->make($abstract, $params);
	}
	
	/**
	 * @inheritdoc
	 */
	public function wrap(callable $fn, array $params = []): \Closure
	{
		return $this->assembler->wrap($fn, $params);
	}
}
