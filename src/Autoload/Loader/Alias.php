<?php
/**
 * Zettacast\Autoload\Loader\Alias class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2016 Rodrigo Siqueira
 */
namespace Zettacast\Autoload\Loader;

use Zettacast\Helper\Aliaser;
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
	 * @var Aliaser Maps alias to classes.
	 */
	protected $data;
	
	/**
	 * Alias loader constructor. This constructor simply sets all of its
	 * properties to empty collections.
	 */
	public function __construct()
	{
		$this->data = new Aliaser;
	}
	
	/**
	 * Tries to load an invoked and not yet loaded class.
	 * @param string $alias Aliased class to be loaded.
	 * @return bool Was the class successfully loaded?
	 */
	public function load(string $alias): bool
	{
		$name = ltrim($alias, '\\');
		
		if(!$this->data->knows($name))
			return false;
		
		return class_alias($this->data->resolve($alias), $name, true);
	}
	
	/**
	 * Resets the loader to its initial state.
	 * @return self Alias loader for method chaining.
	 */
	public function reset()
	{
		$this->data->clear();
		return $this;
	}
	
	/**
	 * Registers a new alias.
	 * @param string $alias Aliased name to be registered.
	 * @param string $target Original name the alias refers to.
	 * @return self Alias loader for method chaining.
	 */
	public function set(string $alias, string $target)
	{
		$this->data->register($alias, $target);
		return $this;
	}
	
	/**
	 * Removes an alias from the map. Classes loaded using the target alias
	 * will not be unloaded in they have already been loaded, but they will
	 * not be able to be loaded using alias anymore.
	 * @param string $alias Alias to be removed.
	 * @return self Alias loader for method chaining.
	 */
	public function remove($alias)
	{
		$this->data->unregister($alias);
		return $this;
	}
	
}
