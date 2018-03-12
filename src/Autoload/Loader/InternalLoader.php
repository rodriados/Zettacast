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
	 * Tries to load an invoked and not yet loaded object. The lookup for
	 * objects happens in framework core first and then it looks for
	 * application specific objects.
	 * @param string $name Name of object to load.
	 * @return bool Was the object successfully loaded?
	 */
	public function load(string $name): bool
	{
		$split = explode('\\', ltrim($name, '\\'));
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
}
