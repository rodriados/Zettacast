<?php
/**
 * Zettacast\Autoload\Loader\InternalLoader class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Autoload\Loader;

use Zettacast\Autoload\LoaderInterface;

/**
 * The internal loader is responsible for loading all objects required by
 * the application or the framework itself.
 * @package Zettacast\Autoload
 * @version 1.0
 */
final class InternalLoader implements LoaderInterface
{
	/**
	 * Maps aliases to objects' full names. The entries in this array should
	 * not override Zettacast objects or unexpected errors may and will occur.
	 * @var array Objects aliases.
	 */
	private $alias = [];
	
	/**
	 * Tries to load an invoked and not yet loaded object. The lookup for
	 * objects happens in framework core first and then it looks for
	 * application specific objects.
	 * @param string $name Name of object to load.
	 * @return bool Was the object successfully loaded?
	 */
	public function load(string $name): bool
	{
		$name = ltrim($name, '\\');
		
		if(isset($this->alias[$name]))
			return class_alias($this->alias[$name], $name, true);
		
		$split = explode('\\', $name);
		$scope = array_shift($split);
		$cname = implode('/', $split);
		
		if(!in_array($scope, [ZETTACAST, 'App']))
			return false;
		
		$fname = ($scope == ZETTACAST)
			? SRCPATH.'/'.$cname.'.php'
			: APPPATH.'/src/'.strtolower($cname).'.php';
			
		if($loaded = file_exists($fname))
			require $fname;
		
		return $loaded;
	}
	
	/**
	 * Registers a new alias.
	 * @param string $alias Aliased name to register.
	 * @param string $target Original name alias refers to.
	 */
	public function alias(string $alias, string $target): void
	{
		$alias = ltrim($alias, '\\');
		$target = ltrim($target, '\\');
		
		$this->alias[$alias] = $target;
	}
	
	/**
	 * Removes an alias from map. Objects loaded using alias will not unload if
	 * they have already been loaded, but they will not be able to load using
	 * their aliased name anymore.
	 * @param string $alias Alias to remove.
	 */
	public function unalias(string $alias): void
	{
		$alias = ltrim($alias, '\\');
		
		if(isset($this->alias[$alias]))
			unset($this->alias[$alias]);
	}
}
