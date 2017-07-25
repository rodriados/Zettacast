<?php
/**
 * Zettacast\Autoload\Loader\Space class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2016 Rodrigo Siqueira
 */
namespace Zettacast\Autoload\Loader;

use Zettacast\Collection\Collection;
use Zettacast\Contract\Autoload\Loader;

/**
 * The Space loader class is responsible for implementing the loading of
 * classes in namespaces explicitly listed along the execution.
 * @package Zettacast\Autoload
 * @version 1.0
 */
class Space
	implements Loader
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
		
		$obj = ltrim($obj, '\\');
		$nspname = explode('\\', $obj);
		$objname = array_pop($nspname);
		
		while($nspname) {
			$space = implode('\\', $nspname);
			
			if($this->data->has($space)) {
				$filename = $this->data->get($space).'/'.$objname.'.php';
				break;
			}
			
			$objname = array_pop($nspname).'/'.$objname;
		}
		
		if(!isset($filename) || !file_exists($filename))
			return false;
		
		require $filename;
		return true;
	}
	
	/**
	 * Resets the loader to its initial state.
	 * @return self Space loader for method chaining.
	 */
	public function reset()
	{
		$this->data->clear();
		return $this;
	}
	
	/**
	 * Registers a new namespace folder.
	 * @param string $space Namespace to be registered.
	 * @param string $folder Folder containing namespace's objects.
	 * @return self Object loader for method chaining.
	 */
	public function set(string $space, string $folder)
	{
		$this->data->set(ltrim($space, '\\'), rtrim($folder, '/'));
		return $this;
	}
	
	/**
	 * Removes an entry from the map. Classes to be loaded using this loader
	 * will not be unloaded if they have already been loaded.
	 * @param string $space Namespace to be removed.
	 * @return self Space loader for method chaining.
	 */
	public function remove($space)
	{
		$this->data->remove(ltrim($space, '\\'));
		return $this;
	}
	
}
