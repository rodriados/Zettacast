<?php
/**
 * Zettacast\Injector\ContextualBinder class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Injector;

/**
 * The contextual binder class is responsible for linking abstractions to
 * different concrete implementations in different creation scopes. This class
 * acts as a parasite to other binders, injecting its information among their
 * data and retrieving it from them when needed.
 * @package Zettacast\Injector
 * @version 1.0
 */
class ContextualBinder implements BinderInterface
{
	/**
	 * Creation scope to which abstractions will be bound to.
	 * @var string Target binding creation scope.
	 */
	protected $scope;
	
	/**
	 * Abstraction binder. This binder will be manipulated so all scoped
	 * bindings are stored in the same place as normal bindings, just in a
	 * slightly different way.
	 * @var BinderInterface Abstraction binder object.
	 */
	protected $binder;
	
	/**
	 * ContextualBinder constructor.
	 * This constructor receives a real binder to which scoped bindings will be
	 * stored onto, acting like a parasite binder.
	 * @param string $scope Creation scope to which abstractions will be bound.
	 * @param BinderInterface $binder Host binder to scoped bindings.
	 */
	public function __construct(string $scope, BinderInterface $binder)
	{
		$this->scope = $scope;
		$this->binder = $binder;
	}
	
	/**
	 * Resolves a binding, or alias to its concrete implementation.
	 * @param string $abstract Requested abstraction name.
	 * @return array Resolved abstraction or null if not found.
	 */
	public function resolve(string $abstract): ?array
	{
		while(is_scalar($abstract) && $this->knows($abstract, $context)) {
			if($context) {
				$result = $this->binder->resolve($abstract.'@'.$this->scope);
				$result['context'] = $this->scope;
				break;
			}
			
			$result = $this->binder->resolve($abstract);
			$abstract = $result['concrete'];
		}
		
		return $result ?? null;
	}
	
	/**
	 * Check whether given abstract is bound to a concrete implementation.
	 * @param string $abstract Abstraction to check.
	 * @param bool &$context Is the known binding contextual?
	 * @return bool Is abstraction known to binder?
	 */
	public function knows(string $abstract, bool &$context = null): bool
	{
		return ($context = $this->binder->knows($abstract.'@'.$this->scope))
		    or $this->binder->knows($abstract);
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
		$this->binder->bind($abstract.'@'.$this->scope, $target, false);
	}
	
	/**
	 * Deletes an abstraction binding.
	 * @param string $abstract Abstraction to unbind.
	 */
	public function unbind(string $abstract): void
	{
		$this->binder->unbind($abstract.'@'.$this->scope);
	}
}
