<?php
/**
 * Zettacast\Injector\Builder class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Injector;

use Closure;
use ReflectionClass;
use ReflectionMethod;
use ReflectionFunction;
use ReflectionParameter;
use Zettacast\Collection\Stack;
use Zettacast\Collection\Collection;
use Zettacast\Injector\Exception\Unresolvable;
use Zettacast\Injector\Exception\Uninstantiable;

/**
 * The builder class is responsible for directly handling dependency injection.
 * This class tries to create new object instances and directly injects any
 * given dependency if needed.
 * @package Zettacast\Injector
 * @version 1.0
 */
class Builder
{
	/**
	 * Collection of shared data. Objects or any kind of data can be shared in
	 * the injector. Shared objects will be instantiated only once.
	 * @var Collection Shared data collection.
	 */
	protected $shared;
	
	/**
	 * Currently active injector instance. This object will be responsible for
	 * handling any abstraction the builder may find.
	 * @var Injector Currently active injector instance.
	 */
	protected $injector;
	
	/**
	 * Building stack. This stack, registers all objects being currently built
	 * in this builder.
	 * @var Stack Building stack.
	 */
	private $stack;
	
	/**
	 * Builder constructor. This constructor simply sets all properties to
	 * the received parameters or empty objects.
	 * @param Injector $injector Currently active injector instance.
	 * @param Collection $shared Collection of shared objects.
	 */
	public function __construct(Injector $injector, Collection &$shared)
	{
		$this->stack = new Stack;
		
		$this->injector = $injector;
		$this->shared = &$shared;
	}
	
	/**
	 * Initializes a new building stack, discarding any remanescent object from
	 * previous unsuccessful builds.
	 * @return static Builder for method chaining.
	 */
	public function init()
	{
		$this->stack->clear();
		return $this;
	}
	
	/**
	 * Resolve the given abstraction and inject dependencies if needed.
	 * @param string $abstract Abstraction to be resolved.
	 * @param array $params Parameters to be used when instantiating.
	 * @return mixed Resolved abstraction.
	 */
	public function make(string $abstract, array $params = [])
	{
		$abstract = $this->injector->identify($abstract);
		
		$context = !$this->stack->empty()
			? $this->injector->when($this->stack->peek())->resolve($abstract)
			: null;

		if(!$context && $this->shared->has($abstract))
			return $this->shared->get($abstract);
		
		$info = $context ?? $this->injector->resolve($abstract);
		$concrete = $info ? $info->concrete : $abstract;
		
		$object = !is_callable($concrete)
			? $abstract == $concrete
				? $this->build($concrete, $params)
				: $this->make($concrete, $params)
			: $concrete($this->injector, ...$params);
		
		if(!$context && $info && $info->shared)
			$this->shared->set($abstract, $object);
		
		return $object;
	}
	
	/**
	 * Wraps a function and solves all of its dependencies.
	 * @param callable $fn Function to be wrapped.
	 * @param array $outer Parameters to be used when invoked.
	 * @return Closure Wrapped function.
	 */
	public function wrap(callable $fn, array $outer = []) : Closure
	{
		$reflector = is_string($fn) && strpos($fn, '::') !== false
			? new ReflectionMethod(...explode('::', $fn))
			: new ReflectionFunction($fn);

		return function(array $inner = []) use($reflector, $outer) {
			return $reflector->invokeArgs($this->resolve(
				$reflector->getParameters(),
				array_merge($outer, $inner)
			));
		};
	}
	
	/**
	 * Builds an instance of the given type and injects its dependencies.
	 * @param string $concrete Type to be instantiated.
	 * @param array $params Parameters to be used when instantiating.
	 * @return mixed Resolved and dependency injected object.
	 * @throws Uninstantiable Type is not instantiable.
	 * @throws Unresolvable The dependency cannot be resolved.
	 */
	protected function build(string $concrete, array $params = [])
	{
		$reflector = new ReflectionClass($concrete);
		
		if(!$reflector->isInstantiable())
			throw new Uninstantiable($concrete);
		
		if(is_null($constructor = $reflector->getConstructor()))
			return $reflector->newInstance();
		
		$this->stack->push($concrete);
		$argv = $this->resolve($constructor->getParameters(), $params);
		$this->stack->pop();
		
		return $reflector->newInstanceArgs($argv);
	}
	
	/**
	 * Resolves all dependencies from a building or wrapping request.
	 * @param ReflectionParameter[] $requested Requested dependencies.
	 * @param array $params Parameters to be used instead of building.
	 * @return array Resolved dependencies.
	 */
	protected function resolve(array $requested, array $params = []) : array
	{
		foreach($requested as $id => $dependency)
			if(isset($params[$dependency->name]))
				$solved[] = $params[$dependency->name];
			elseif(isset($params[$id]))
				$solved[] = $params[$id];
			elseif(!is_null($dependency->getClass()))
				$solved[] = $this->buildObject($dependency);
			else /* not explicitly given param nor typed argument */
				$solved[] = $this->buildPrimitive($dependency);
		
		return $solved ?? [];
	}
	
	/**
	 * Tries to resolve a primitive dependency.
	 * @param ReflectionParameter $param Parameter to be resolved.
	 * @return mixed Resolved primitive.
	 * @throws Unresolvable Primitive value could not be resolved.
	 */
	private function buildPrimitive(ReflectionParameter $param)
	{
		$context = $this->stack->peek();
		$info = $this->injector->when($context)->resolve('$'.$param->name);
		
		if(!is_null($info))
			return is_callable($info->concrete)
				? ($info->concrete)($this->injector)
				: $this->make($info->concrete);
		
		if($param->isDefaultValueAvailable())
			return $param->getDefaultValue();
			
		throw new Unresolvable($param);
	}
	
	/**
	 * Tries to resolve an object dependency.
	 * @param ReflectionParameter $param Parameter to be resolved.
	 * @return mixed Resolved object.
	 * @throws Unresolvable Dependency could not be resolved.
	 */
	private function buildObject(ReflectionParameter $param)
	{
		try {
			return $this->make($param->getClass()->getName());
		} catch(\Exception $e) {
			if($param->isOptional())
				return $param->getDefaultValue();
			
			throw new Unresolvable($param, $e);
		}
	}
	
}
