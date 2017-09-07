<?php
/**
 * Zettacast\Config\Repository class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Config;

use Zettacast\Collection\Dot;
use Zettacast\Collection\Sequence;
use Zettacast\Filesystem\Filesystem;

/**
 * Manages all configuration data known to the framework. Configuration files
 * are found in the application folder.
 * @version 1.0
 */
class Repository
{
	/**
	 * Holds all data contained by the repository.
	 * @var Dot Repository data.
	 */
	protected $dot;
	
	/**
	 * Lists all files that have already been loaded by this repository.
	 * @var Sequence List of loaded files.
	 */
	protected $files;
	
	/**
	 * Configuration folder, that holds all files.
	 * @var Filesystem Manages all files in configuration folder.
	 */
	protected $folder;
	
	/**
	 * Repository constructor.
	 * This constructor simply initializes all instance properties.
	 * @param string $location Configuration files folder location.
	 */
	public function __construct(string $location)
	{
		$this->dot = new Dot;
		$this->files = new Sequence;
		$this->folder = new Filesystem($location);
	}
	
	/**
	 * Retrieves a configuration value from the repository. If no correspondent
	 * value can be found, the default one is returned.
	 * @param string $key Configuration value to be retrieved.
	 * @param mixed $default Default value to be returned if key is not found.
	 * @return mixed The retrieved value.
	 */
	public function get(string $key, $default = null)
	{
		$group = explode('.', $key)[0];
		$this->load($group);
		
		return $this->dot->get($key, $default);
	}
	
	/**
	 * Loads a configuration file to memory.
	 * @param string $file File to be loaded.
	 * @return bool Did the file load correctly?
	 */
	public function load(string $file) : bool
	{
		if(in_array($file, $this->files->all()))
			return true;
		
		if(!$this->folder->has($file.'.php'))
			return false;
		
		$this->files->push($file);
		$this->dot->set($file, include $this->folder->realpath($file.'.php'));
		return true;
	}
	
	/**
	 * Makes a configuration value not available for retrieving. Although the
	 * current request will be not able to access such value anymore, the
	 * configuration files are untouched.
	 * @param string $key Key to be removed.
	 * @return static Instance for method chaining.
	 */
	public function remove(string $key)
	{
		$this->dot->remove($key);
		return $this;
	}
	
	/**
	 * Modifies a configuration value, thus making the old value unavailable
	 * for retrieving. But similarly to removing it, the configuration files
	 * are untouched.
	 * @param string $key Key to be modified.
	 * @param mixed $value Value to substitute the old one.
	 * @return static Instance for method chaining.
	 */
	public function set(string $key, $value)
	{
		$this->dot->set($key, $value);
		return $this;
	}
	
}
