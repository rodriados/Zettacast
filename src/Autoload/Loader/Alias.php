<?php
/**
 * Zettacast\Autoload\Loader\Alias class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2016 Rodrigo Siqueira
 */
namespace Zettacast\Autoload\Loader;

use Zettacast\Contract\Autoload\Loader;

/**
 * The Alias loader class is responsible for implementing the use of class
 * alias, allowing classes and namespaces to be renamed in execution time.
 * @package Zettacast\Autoload
 * @version 1.0
 */
final class Alias
	implements Loader
{
	/**
	 * Maps alias to classes' full names. The entries in this array should not
	 * override Zettacast classes or unexpected errors may occur.
	 * @var array Maps alias to classes.
	 */
	protected $alias;
	
	/**
	 * Tries to load an invoked and not yet loaded class.
	 * @param string $alias Aliased class to be loaded.
	 * @return bool Was the class successfully loaded?
	 */
	public function load(string $alias): bool
	{
		$aname = ltrim($alias, '\\');
		
		if(!isset($this->alias[$aname]))
			return false;
		
		return class_alias($this->alias[$aname], $aname);
	}
	
	/**
	 * Resets the loader to its initial state.
	 * @return void No return expected.
	 */
	public function reset()
	{
		$this->alias = [];
	}
	
	/**
	 * Adds new alias map entries. Conflicting entries will simply be
	 * overwritten to the newest value.
	 * @param array $map Map of aliases to be added.
	 */
	public function add(array $map)
	{
		foreach($map as $target => $original)
			$this->alias[ltrim($target, '\\')] = ltrim($original, '\\');
	}
	
	/**
	 * Removes an alias from the map. Classes loaded using the target alias
	 * will not be unloaded in they have already been loaded, but they will
	 * not be able to be loaded using alias anymore.
	 * @param array|string $alias Alias to be removed.
	 */
	public function del($alias)
	{
		foreach((array)$alias as $target)
			if(isset($this->alias[$target]))
				unset($this->alias[$target]);
	}
	
	/**
	 * Resets and erases all previous aliases and put new ones in the list.
	 * @param array $map New alias mappings.
	 */
	public function set(array $map)
	{
		$this->reset();
		$this->add($map);
	}
	
}
