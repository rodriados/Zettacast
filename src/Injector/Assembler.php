<?php
/**
 * Zettacast\Injector\Assembler class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Injector;

use Zettacast\Collection\Stack;

/**
 * The assembler class is responsible for directly handling dependency
 * injection, and assembling object instances. This class tries to create new
 * object instances and directly injects any given dependency if needed.
 * @package Zettacast\Injector
 * @version 1.0
 */
class Assembler
{
	/**
	 * Currently active injector instance. This object will be responsible for
	 * resolving any abstractions the assembler may find.
	 * @var InjectorInterface Currently active injector instance.
	 */
	protected $injector;
	
	/**
	 * Building stack. This stack, registers all objects being currently built
	 * in this assembler.
	 * @var Stack Building stack.
	 */
	private $stack;
	
	/**
	 * Assembler constructor.
	 * This constructor simply sets all properties to the received parameters
	 * or empty objects.
	 * @param InjectorInterface $injector Currently active injector instance.
	 */
	public function __construct(InjectorInterface $injector)
	{
		$this->injector = $injector;
		$this->stack = new Stack;
	}
	
	/**
	 * Resolves given abstraction and inject dependencies if needed.
	 * @param string $abstract Abstraction to resolve.
	 * @param array $params Parameters to use when instantiating.
	 * @return mixed Resolved abstraction.
	 */
	public function make(string $abstract, array $params = [])
	{
		$bond = $this->stack->empty()
			? $this->injector->resolve($abstract)
			: $this->injector->when($this->stack->peek())->resolve($abstract);
		
		$concrete = $bond['concrete'] ?? $abstract;
		$context = $bond['context'] ?? false;
		$shared = $bond['shared'] ?? false;
		
		if(!$context && is_scalar($concrete) && $this->injector->has($concrete))
			return $this->injector->get($concrete);
		
		$object = !is_callable($concrete)
			? $abstract == $concrete
				? $this->build($concrete, $params)
				: $this->make($concrete, $params)
			: $concrete(...$params);
		
		if(!$context && $shared)
			$this->injector->set($abstract, $object);
		
		return $object;
	}
	
	/**
	 * Wraps a function and resolve all of its dependencies.
	 * @param callable $fn Function to wrap.
	 * @param array $outer Parameters to use when invoked.
	 * @return \Closure Wrapped function.
	 * @throws InjectorException The given method cannot be wrapped.
	 */
	public function wrap(callable $fn, array $outer = []): \Closure
	{
		$reflect = is_array($fn)
			? new \ReflectionMethod(...$fn)
			: new \ReflectionFunction($fn);
		
		if($reflect instanceof \ReflectionMethod
		   && !$reflect->isStatic() && !is_object($fn[0]))
			throw InjectorException::missing(...$fn);
		
		$call = $reflect instanceof \ReflectionMethod
			? [$reflect->isStatic() ? null : $fn[0]]
			: [];
		
		return function(array $inner = []) use($reflect, $outer, $call) {
			$this->stack->push($reflect->name);
			$args = array_merge($inner, $outer);
			$call[] = $this->resolve($reflect->getParameters(), $args);
			$this->stack->pop();
			
			return $reflect->invokeArgs(...$call);
		};
	}
	
	/**
	 * Builds an instance of the given type and injects its dependencies.
	 * @param string $concrete Type to instantiate.
	 * @param array $params Parameters to use when instantiating.
	 * @return mixed Resolved and dependency injected object.
	 * @throws InjectorException The object could not be assembled.
	 */
	protected function build(string $concrete, array $params = [])
	{
		if(!class_exists($concrete))
			throw InjectorException::inexistant($concrete);
		
		$reflect = new \ReflectionClass($concrete);
		
		if(!$reflect->isInstantiable())
			throw InjectorException::uninstantiable($concrete);
		
		if(is_null($constructor = $reflect->getConstructor()))
			return $reflect->newInstance();
		
		$this->stack->push($concrete);
		$argv = $this->resolve($constructor->getParameters(), $params);
		$this->stack->pop();
		
		return $reflect->newInstanceArgs($argv);
	}
	
	/**
	 * Resolves all dependencies from a building or wrapping request.
	 * @param \ReflectionParameter[] $requested Requested dependencies.
	 * @param array $params Parameters to use instead of building.
	 * @return array Resolved dependencies.
	 */
	protected function resolve(array $requested, array $params = []): array
	{
		foreach($requested as $id => $dependency)
			if(isset($params[$dependency->name]))
				$solved[] = $params[$dependency->name];
			elseif(isset($params[$id]))
				$solved[] = $params[$id];
			elseif(!is_null($dependency->getClass()))
				$solved[] = $this->mount($dependency);
			else /* not explicitly given param nor typed argument */
				$solved[] = $this->guess($dependency);

		return $solved ?? [];
	}
	
	/**
	 * Tries to resolve a primitive dependency.
	 * @param \ReflectionParameter $param Parameter to resolve.
	 * @return mixed Resolved primitive.
	 * @throws InjectorException Parameter could not be resolved.
	 */
	private function guess(\ReflectionParameter $param)
	{
		$context = $this->stack->peek();
		$info = $this->injector->when($context)->resolve('$'.$param->name);
		
		if(isset($info['concrete']))
			return $info['concrete'] instanceof \Closure
				? ($info['concrete'])($this->injector)
				: $info['concrete'];
		
		if($param->isDefaultValueAvailable())
			return $param->getDefaultValue();
		
		$this->stack->pop();
		throw InjectorException::unresolvable($context, $param->name);
	}
	
	/**
	 * Tries to resolve an object dependency.
	 * @param \ReflectionParameter $param Parameter to resolve.
	 * @return mixed Resolved object.
	 * @throws InjectorException Exception thrown resolving parameter.
	 */
	private function mount(\ReflectionParameter $param)
	{
		try {
			return $this->make($param->getClass()->getName());
		}
		
		catch(InjectorException $e) {
			if($param->isOptional())
				return $param->getDefaultValue();
			
			$this->stack->pop();
			throw $e;
		}
	}
}
