<?php
/**
 * Zettacast\Config\Config class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Config;

use Zettacast\Filesystem\Filesystem;
use Zettacast\Collection\DotCollection;

class Config
{
	/**
	 * Holds all data contained in configuration container.
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
	 * Sets the target folder for configuration lookup.
	 * @param string $folder The directory containing config files.
	 */
	public function __construct(string $folder = CFGPATH)
	{
		$this->data = new DotCollection;
		$this->folder = new Filesystem($folder);
	}
	
	/**
	 * Retrieves a configuration value from directory. If no correspondent
	 * value can be found, the default one is returned.
	 * @param string $key Configuration value to retrieve.
	 * @param mixed $default Default value to return if key is not found.
	 * @return mixed The retrieved value.
	 */
	public function get(string $key, $default = null)
	{
		$file = explode('.', $key, 2)[0];
		
		return $this->has($file) || $this->load($file)
			? $this->data->get($key, $default)
			: $default;
	}
	
	/**
	 * Checks whether a value key exists within directory.
	 * @param string $key Key to check existance.
	 * @return bool Is value key present in directory?
	 */
	public function has(string $key): bool
	{
		$file = explode('.', $key, 2)[0];
		
		return $this->data->has($file) || $this->load($file)
			? $this->data->has($key)
			: false;
	}
	
	/**
	 * Loads configuration file to memory.
	 * @param string $file File to load.
	 * @return bool Did the file load correctly?
	 */
	public function load(string $file): bool
	{
		static $loaded;
		
		if(isset($loaded[$file]) && $loaded[$file])
			return $loaded[$file];
		
		if($fname = $this->folder->info($file.'.php', 'realpath'))
			$this->data->set($file, require $fname);
		
		return $loaded[$file] = (bool)$fname;
	}
}
