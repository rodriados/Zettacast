<?php
/**
 * Zettacast\Helper\Aliaser class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Helper;

use Zettacast\Collection\Collection;

/**
 * The Aliaser class is responsible for managing aliases. An alias must always
 * be an string, but it can represent any kind of data.
 * @package Zettacast\Helper
 * @version 1.0
 */
class Aliaser
{
	/**
	 * Collection of aliases. Stores all aliases registered in the object.
	 * @var Collection Aliases storage.
	 */
	protected $data;
	
	/**
	 * Aliaser constructor. This constructor simply sets all of its properties
	 * to empty collections.
	 */
	public function __construct()
	{
		$this->data = new Collection;
	}
	
	/**
	 * Clears and removes all known aliases and returns the old ones.
	 * @return Collection All previously known aliases.
	 */
	public function clear(): Collection
	{
		$old = $this->data;
		$this->data = new Collection;
		
		return $old;
	}
	
	/**
	 * Checks whether the given alias is known.
	 * @param string $alias Alias to be checked.
	 * @return bool Is alias known?
	 */
	public function knows(string $alias): bool
	{
		return $this->data->has($alias);
	}
	
	/**
	 * Registers a new alias.
	 * @param string $alias Alias name to be registered.
	 * @param mixed $target Aliased object.
	 * @return $this Aliaser for method chaining.
	 */
	public function register(string $alias, $target)
	{
		if($alias != $target)
			$this->data->set($alias, $target);
		
		return $this;
	}
	
	/**
	 * Resolves an alias.
	 * @param string $alias Alias name to be resolved.
	 * @return mixed Resolved value.
	 */
	public function identify(string $alias)
	{
		return $this->data->has($alias)
			? $this->identify($this->data->get($alias))
			: $alias;
	}
	
	/**
	 * Unregisters an alias.
	 * @param string $alias Alias name to be unregistered.
	 * @return $this Aliaser for method chaining.
	 */
	public function unregister(string $alias)
	{
		$this->data->del($alias);
		return $this;
	}
	
}
