<?php
/**
 * Zettacast\Facade\Autoload class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Facade;

use Zettacast\Autoload\LoaderInterface;
use Zettacast\Autoload\Loader\ObjectLoader;
use Zettacast\Autoload\Loader\NamespaceLoader;

/**
 * Zettacast's Autoload façade class.
 * This class exposes the Autoload package methods to external usage.
 * @method static has(string $name): bool
 * @method static get(string $name): ?LoaderInterface
 * @method static unregister(string $name): void
 * @method static unalias(string $name): void
 * @version 1.0
 */
final class Autoload extends Facade
{
	/**
	 * Registers a loader in autoload stack. The autoload function will be
	 * responsible for automatically loading all objects invoked by framework
	 * or by application. Catches special cases for object or namespace loader.
	 * @param string $name The name to use for loader identification.
	 * @param mixed $param A loader to register or data for special loaders.
	 * @return bool Was the loader successfully registered?
	 */
	public static function register(string $name, $param): bool
	{
		if($param instanceof LoaderInterface)
			return self::i()->register($name, $param);

		$special = ['object', 'namespace'];
		
		if(!in_array($name, $special) || self::i()->has($name) || !$param)
			return false;
		
		return $name == 'namespace'
			? self::i()->register($name, new NamespaceLoader($param))
			: self::i()->register($name, new ObjectLoader($param));
	}
	
	/**
	 * Registers a new alias. Checks if a list of a aliases was given.
	 * @param string|array|\Traversable $alias Alias name or a map of aliases.
	 * @param string $target Original name alias refers to.
	 */
	public static function alias($alias, string $target = null): void
	{
		$i = self::i();
		
		if(!listable($alias))
			$alias = [$alias => $target];
		
		foreach($alias as $name => $target)
			$i->alias($name, $target);
	}
	
	
	/**
	 * Informs what the façaded object accessor is, allowing it to be further
	 * used when calling façaded methods.
	 * @return mixed Façaded object accessor.
	 */
	protected static function accessor()
	{
		return 'autoload';
	}
}
