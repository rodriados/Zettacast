<?php
/**
 * Zettacast\Autoload\Loader\NamespaceLoader class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2016 Rodrigo Siqueira
 */
namespace Zettacast\Autoload\Loader;

use Zettacast\Collection\Collection;
use Zettacast\Contract\Autoload\LoaderInterface;

/**
 * The Space loader class is responsible for implementing the loading of
 * classes in namespaces explicitly listed along the execution.
 * @package Zettacast\Autoload
 * @version 1.0
 */
class NamespaceLoader implements LoaderInterface
{
	/**
	 * Listed namespaces. The entries in this collection should not override
	 * Zettacast namespaces or unexpected errors may occur.
	 * @var Collection Maps namespaces to their actual paths.
	 */
	protected $data;
	
	/**
	 * Space loader constructor. This constructor simply sets all of its
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
		if($this->data->empty())
			return false;
		
		$name = explode('\\', ltrim($obj, '\\'));
		$objfile = array_pop($name).'.php';
		
		while($name && !$this->data->has($space = implode('\\', $name)))
			$objfile = array_pop($name).'/'.$objfile;
		
		if($name && isset($space))
			$fname = $this->data->get($space).'/'.$objfile;
		
		if(isset($fname) && $loaded = file_exists($fname))
			require $fname;
		
		return $loaded ?? false;
	}
	
	/**
	 * Registers a new namespace folder.
	 * @param string $space Namespace to be registered.
	 * @param string $folder Folder containing namespace's objects.
	 * @return $this Object loader for method chaining.
	 */
	public function set(string $space, string $folder)
	{
		$space = ltrim($space, '\\');
		$folder = rtrim($folder, '/');
		
		$this->data->set($space, $folder);
		return $this;
	}
	
	/**
	 * Removes an entry from the map. Classes to be loaded using this loader
	 * will not be unloaded if they have already been loaded.
	 * @param string $space Namespace to be removed.
	 * @return $this Space loader for method chaining.
	 */
	public function del($space)
	{
		$space = ltrim($space, '\\');
		
		$this->data->del($space);
		return $this;
	}
	
}
