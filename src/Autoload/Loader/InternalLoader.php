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

final class InternalLoader implements LoaderInterface
{
	/**
	 * @inheritdoc
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
