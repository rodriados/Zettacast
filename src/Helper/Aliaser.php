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
use Zettacast\Contract\StorageInterface;

/**
 * The Aliaser class is responsible for managing aliases. An alias must always
 * be an string, but it can represent any kind of data.
 * @package Zettacast\Helper
 * @version 1.0
 */
class Aliaser implements StorageInterface
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
	 * Retrieves an alias known to the object.
	 * @param mixed $alias Requested alias value.
	 * @return mixed Requested element or the original alias.
	 */
	public function get($alias)
	{
		while($this->data->has($alias))
			$alias = $this->data->get($alias);
		
		return $alias;
	}
	
	/**
	 * Checks whether the given alias is known.
	 * @param string $alias Alias to be checked.
	 * @return bool Is alias known?
	 */
	public function has($alias): bool
	{
		return $this->data->has($alias);
	}
	
	/**
	 * Registers a new alias.
	 * @param string $alias Alias name to be registered.
	 * @param mixed $target Aliased object.
	 * @return $this Aliaser for method chaining.
	 */
	public function set($alias, $target)
	{
		if($alias != $target)
			$this->data->set($alias, $target);
		
		return $this;
	}
	
	/**
	 * Unregisters an alias.
	 * @param string $alias Alias name to be unregistered.
	 * @return $this Aliaser for method chaining.
	 */
	public function del($alias)
	{
		$this->data->del($alias);
		return $this;
	}
	
	/**
	 * Clears and removes all known aliases and returns the old ones.
	 * @return array All previously known aliases.
	 */
	public function clear(): array
	{
		return $this->data->clear();
	}
	
}
