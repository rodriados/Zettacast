<?php
/**
 * Zettacast\Autoload\Loader\Object class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2016 Rodrigo Siqueira
 */
namespace Zettacast\Autoload\Loader;

use Zettacast\Contract\Autoload\Loader;

/**
 * The Object loader class is responsible for implementing the loading of
 * objects explicitly listed along the execution.
 * @package Zettacast\Autoload
 * @version 1.0
 */
class Object
	implements Loader
{
	/**
	 * Listed objects. The entries in this array should not override Zettacast
	 * classes or unexpected errors may occur.
	 * @var array Maps objects to their actual paths.
	 */
	protected $objects;
	
	/**
	 * Tries to load an invoked and not yet loaded object.
	 * @param string $obj Object to be loaded.
	 * @return bool Was the object successfully loaded?
	 */
	public function load(string $obj): bool
	{
		$objname = ltrim($obj, '\\');
		
		if(empty($this->objects) or !isset($this->objects[$objname]))
			return false;
		
		$fname = $this->objects[$objname];
		
		if(!file_exists($fname))
			return false;
		
		require $fname;
		return true;
	}
	
	/**
	 * Resets the loader to its initial state.
	 * @return void No return expected.
	 */
	public function reset()
	{
		$this->objects = [];
	}
	
	/**
	 * Adds new object map entries. Conflicting entries will simply be
	 * overwritten to the newest value.
	 * @param array $map Map of objects to be added.
	 */
	public function add(array $map)
	{
		foreach($map as $objname => $objpath)
			$this->objects[ltrim($objname, '\\')] = $objpath;
	}
	
	/**
	 * Removes an entry from the map. Classes to be loaded using this loader
	 * will not be unloaded if they have already been loaded.
	 * @param array|string $objlist Objects to be removed.
	 */
	public function del($objlist)
	{
		foreach((array)$objlist as $objname)
			if(isset($this->objects[$objname]))
				unset($this->objects[$objname]);
	}
	
	/**
	 * Resets and erases all previous entries and put new ones in the list.
	 * @param array $map New object mappings.
	 */
	public function set(array $map)
	{
		$this->reset();
		$this->add($map);
	}
	
}
