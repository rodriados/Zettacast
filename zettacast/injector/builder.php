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
use Exception;
use ReflectionClass;
use ReflectionMethod;
use ReflectionFunction;
use Zettacast\Collection\Basic as Collection;

/**
 * The builder class is responsible for directly handling dependency injection.
 * This class tries to create new object instances and directly injects any
 * given dependency if needed.
 * @package Zettacast\Injector
 * @version 1.0
 */
class Builder {
	
	/**
	 * Currently active injector instance. This object will be responsible for
	 * handling any abstraction the builder may find.
	 * @var Injector Currently active injector instance.
	 */
	protected $injector;
	
	/**
	 * Building stack collection. This collection acts as a stack, registering
	 * the objects being currently built at the moment.
	 * @var Collection Building stack.
	 */
	private $stack;
	
	/**
	 * Builder constructor. This constructor simply sets all properties to
	 * the received parameters or empty objects.
	 * @param Injector $injector Currently active injector instance.
	 */
	public function __construct(Injector $injector) {
		
		$this->injector = $injector;
		$this->stack = new Collection;
		
	}
	
	/**
	 * Builds an instance of the given type and injects its dependencies.
	 * @param string $concrete Type to be instantiated.
	 * @return mixed Resolved type.
	 * @throws \Exception Type is not instantiable.
	 */
	public function make(string $concrete) {
		
		$reflector = new ReflectionClass($concrete);
		
		if(!$reflector->isInstantiable())
			throw new Exception($concrete.' is not instantiable');
		
		if(is_null($constructor = $reflector->getConstructor()))
			return new $concrete;
		
		$this->stack->push($concrete);
		
		foreach($constructor->getParameters() as $dependency)
			$params[] = is_null($dependency->getClass())
				? $this->rprimitive($dependency)
				: $this->rclass($dependency);
		
		$this->stack->pop();
		
		return $reflector->newInstanceArgs($params ?? []);
		
	}
	
	/**
	 * Wraps a function and solves all of its dependencies.
	 * @param callable $fn Function to be wrapped.
	 * @param array $params Parameters to be used when invoked.
	 * @return Closure Wrapped function.
	 */
	public function wrap(callable $fn, array $params = []) {
		
		$reflector = is_string($fn) && strpos($fn, '::') !== false
			? new ReflectionMethod(...explode('::', $fn))
			: new ReflectionFunction($fn);
		
		foreach($reflector->getParameters() as $param)
			if(array_key_exists($param->name, $params))
				$callparam[] = $params[$param->name];
			elseif($v = $param->getClass())
				$callparam[] = $this->injector->make($v->name);
			elseif($param->isDefaultValueAvailable())
				$callparam[] = $param->getDefaultValue();
			else
				$callparam[] = null;
		
		$callparam = array_merge($callparam ?? [], $params);
			
		return function() use($reflector, $callparam) {
			return $reflector->invokeArgs($callparam);
		};
			
	}
	
	/**
	 * Tries to resolve a primitive dependency.
	 * @param \ReflectionParameter $param Parameter to be resolved.
	 * @return mixed Resolved primitive.
	 * @throws Exception Primitive value could not be resolved.
	 */
	protected function rprimitive(\ReflectionParameter $param) {
		
		$context = $this->stack->end();
		$link = $this->injector->resolve('$'.$param->name, $context)->concrete;
		
		if(!is_null($link))
			return $link instanceof Closure
				? $link($this->injector) : $link;
		
		if($param->isDefaultValueAvailable())
			return $param->getDefaultValue();
		
		throw new Exception(sprintf('Unresolvable %s in %s::%s',
			$param,
			!is_null($c = $param->getDeclaringClass()) ? $c->getName() : '',
			$param->getDeclaringFunction()->getName() ?? ''));
		
	}
	
	/**
	 * Tries to resolve a object dependency.
	 * @param \ReflectionParameter $param Parameter to be resolved.
	 * @return mixed Resolved object.
	 * @throws Exception Object dependency could not be resolved.
	 */
	protected function rclass(\ReflectionParameter $param) {
		
		try {
			
			return $this->injector->make($param->getClass()->getName());
			
		}
		
		catch(Exception $e) {
			
			if($param->isOptional())
				return $param->getDefaultValue();
			
			throw $e;
			
		}
		
	}
	
}
