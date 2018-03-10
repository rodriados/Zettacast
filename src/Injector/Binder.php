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
	 * @inheritdoc
	 */
	public function resolve(string $abstract)
	{
		var_dump($this->bindings);
		
		while(is_scalar($abstract) && $this->knows($abstract)) {
			$result = $this->bindings->get($abstract);
			$abstract = $result->concrete;
		}
		
		return $result ?? null;
	}
	
	/**
	 * @inheritdoc
	 */
	public function knows(string $abstract): bool
	{
		return $this->bindings->has($abstract);
	}
	
	/**
	 * @inheritdoc
	 */
	public function bind(string $abstract, $target, bool $share = false): void
	{
		$this->bindings->set($abstract, $s = (object)[
			'concrete' => $target,
			'shared'   => $share,
		]);
	}
	
	/**
	 * @inheritdoc
	 */
	public function unbind(string $abstract): void
	{
		$this->bindings->del($abstract);
	}
}
