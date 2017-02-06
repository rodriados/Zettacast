<?php
/**
 * Autoload\Loader\Alias class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2016 Rodrigo Siqueira
 */
namespace Zettacast\Autoload\Loader;

/*
 * Declaration of exterior resources needed. We declare the resources we are
 * going to need so we can skip all namespace bureaucracy.
 */
use Zettacast\Autoload\Loader;

/**
 * The Alias loader class is responsible for implementing the use of class
 * alias, allowing classes and namespaces to be renamed in execution time.
 * @package Zettacast\Autoload
 * @version 1.0
 */
final class Alias implements Loader {
	
	/**
	 * Maps alias to classes' full names. The entries in this array should not
	 * override Zettacast classes or unexpected errors may occur.
	 * @var array Maps alias to classes.
	 */
	protected $alias;
	
	/**
	 * Alias loader constructor. Initializes the class and set values to
	 * instance properties.
	 */
	public function __construct() {
		
		$this->reset();
		
	}
	
	/**
	 * Tries to load an invoked and not yet loaded class.
	 * @param string $alias Aliased class to be loaded.
	 * @return bool Was the class successfully loaded?
	 */
	public function load(string $alias): bool {
		
		if(!isset($this->alias[$alias]))
			return false;
		
		return class_alias($this->alias[$alias], $alias);
		
	}
	
	/**
	 * Resets the loader to its initial state.
	 */
	public function reset() {
		
		$this->alias = [];
		
	}
	
	/**
	 * Adds new alias map entries. Conflicting entries will simply be
	 * overwritten to the newest value.
	 * @param array $map Map of aliases to be added.
	 */
	public function addAlias(array $map) {
		
		foreach($map as $target => $original)
			$this->alias[ltrim($target, '\\')] = ltrim($original, '\\');
		
	}
	
	/**
	 * Removes an alias from the map. Classes loaded using the target alias
	 * will not be unloaded in they have already been loaded, but they will
	 * not be able to be loaded using alias anymore.
	 * @param array|string $alias Alias to be removed.
	 */
	public function delAlias($alias) {
		
		foreach((array)$alias as $target)
			if(isset($this->alias[$target]))
				unset($this->alias[$target]);
		
	}
	
	/**
	 * Resets and erases all previous aliases and put new ones in the list.
	 * @param array $map New alias mappings.
	 */
	public function setAlias(array $map) {
		
		$this->reset();
		$this->addAlias($map);
		
	}
	
}
