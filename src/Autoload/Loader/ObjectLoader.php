<?php
/**
 * Zettacast\Autoload\Loader\ObjectLoader class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2016 Rodrigo Siqueira
 */
namespace Zettacast\Autoload\Loader;

use Zettacast\Collection\Collection;
use Zettacast\Contract\Autoload\LoaderInterface;

/**
 * The Object loader class is responsible for implementing the loading of
 * objects explicitly listed along the execution.
 * @package Zettacast\Autoload
 * @version 1.0
 */
class ObjectLoader implements LoaderInterface
{
	/**
	 * Listed objects. The entries in this collection should not override
	 * Zettacast classes or unexpected errors may occur.
	 * @var Collection Mappings of objects to their actual paths.
	 */
	protected $data;
	
	/**
	 * Object loader constructor. This constructor simply sets all of its
	 * properties to empty collections.
	 */
	public function __construct()
	{
		$this->data = new Collection;
	}
	
	/**
	 * Tries to load an invoked and not yet loaded object.
	 * @param string $obj Object to be loaded.
	 * @return bool Was the object successfully loaded?
	 */
	public function load(string $obj): bool
	{
		$obj = ltrim($obj, '\\');
		
		if(
			!$this->data->has($obj) ||
		    !file_exists($filename = $this->data->get($obj))
		)
			return false;
		
		require $filename;
		return true;
	}
	
	/**
	 * Resets the loader to its initial state.
	 * @return $this Object loader for method chaining.
	 */
	public function reset()
	{
		$this->data->clear();
		return $this;
	}
	
	/**
	 * Registers a new object file.
	 * @param string $obj Object name to be registered.
	 * @param string $file File in which object can be found.
	 * @return $this Object loader for method chaining.
	 */
	public function set(string $obj, string $file)
	{
		$obj = ltrim($obj, '\\');
		
		$this->data->set($obj, $file);
		return $this;
	}
	
	/**
	 * Removes an entry from the map. Classes to be loaded using this loader
	 * will not be unloaded if they have already been loaded.
	 * @param string $obj Object to be removed.
	 * @return $this Object loader for method chaining.
	 */
	public function del(string $obj)
	{
		$obj = ltrim($obj, '\\');
		
		$this->data->del($obj);
		return $this;
	}
	
}
