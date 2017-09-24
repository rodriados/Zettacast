<?php
/**
 * Config façade file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Facade;

use Zettacast\Helper\Facade;
use Zettacast\Filesystem\Filesystem;
use Zettacast\Contract\SingletonTrait;
use Zettacast\Collection\DotCollection;

/**
 * Zettacast's Config façade class.
 * This façade is responsible for accessing the application and framework's
 * configuration files.
 * @version 1.0
 */
final class Config extends Facade
{
	use SingletonTrait;
	
	/**
	 * Holds all data contained in the warehouse.
	 * @var DotCollection Repository data.
	 */
	protected $data;
	
	/**
	 * Configuration folder, that holds all files.
	 * @var Filesystem Manages all files in configuration folder.
	 */
	protected $folder;

	/**
	 * Config constructor.
	 * This constructor simply initializes all instance properties.
	 */
	protected function __construct()
	{
		$this->data = new DotCollection;
		$this->folder = new Filesystem(CONFIGPATH);
	}
	
	/**
	 * Retrieves a configuration value from the directory. If no correspondent
	 * value can be found, the default one is returned.
	 * @param string $key Configuration value to be retrieved.
	 * @param mixed $default Default value to be returned if key is not found.
	 * @return mixed The retrieved value.
	 */
	public static function get(string $key, $default = null)
	{
		$file = explode('.', $key, 2)[0];
		
		return self::i()->data->has($file) || self::load($file)
			? self::i()->data->get($key, $default)
			: $default;
	}
	
	/**
	 * Checks whether a value key exists within the directory.
	 * @param string $key Key to be searched for.
	 * @return bool Is value key present in the directory?
	 */
	public static function has(string $key): bool
	{
		$file = explode('.', $key, 2)[0];
		
		return self::i()->data->has($file) || self::load($file)
			? self::i()->data->has($key)
			: false;
	}
	
	/**
	 * Loads configuration file to memory.
	 * @param string $file File to be loaded.
	 * @return bool Did the file load correctly?
	 */
	public static function load(string $file): bool
	{
		static $loaded;
		
		if(isset($loaded[$file]))
			return $loaded[$file];
		
		if($fname = self::i()->folder->info($file.'.php', 'realpath'))
			self::i()->data->set($file, require $fname);
			
		return $loaded[$file] = (bool)$fname;
	}
	
	/**
	 * Informs what the façaded object accessor is, allowing it to be further
	 * used when calling façaded methods.
	 * @return mixed Façaded object accessor.
	 */
	protected static function accessor()
	{
		return self::i();
	}
	
}
