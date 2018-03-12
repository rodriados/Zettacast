<?php
/**
 * Zettacast\Autoload\Loader\ObjectLoader class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Autoload\Loader;

use Zettacast\Collection\Collection;
use Zettacast\Autoload\LoaderInterface;

/**
 * The object loader class is responsible for loading objects in paths
 * explicitly listed along framework execution.
 * @package Zettacast\Autoload
 * @version 1.0
 */
class ObjectLoader implements LoaderInterface
{
	/**
	 * Listed objects. The entries in this collection should not override
	 * Zettacast objects or unexpected errors may occur.
	 * @var Collection Maps objects to their actual paths.
	 */
	protected $data;
	
	/**
	 * ObjectLoader constructor.
	 * This constructor simply sets all of its properties to empty collections.
	 * @param array $data Initial object bindings.
	 */
	public function __construct(array $data = [])
	{
		$this->data = new Collection;
		
		foreach($data as $key => $value)
			$this->set($key, $value);
	}
	
	/**
	 * Tries to load an invoked and not yet loaded object.
	 * @param string $obj Object to load.
	 * @return bool Was the object successfully loaded?
	 */
	public function load(string $obj): bool
	{
		if($this->data->empty())
			return false;
		
		$obj = ltrim($obj, '\\');
		
		if(($fname = $this->data[$obj]) && ($loaded = file_exists($fname)))
			require $fname;
		
		return $loaded ?? false;
	}
	
	/**
	 * Registers a new object file path.
	 * @param string $obj Object name to register.
	 * @param string $file File in which object can be found.
	 */
	public function set(string $obj, string $file): void
	{
		$obj = ltrim($obj, '\\');
		$this->data->set($obj, $file);
	}
	
	/**
	 * Removes an entry from map. Objects to load using this loader will not
	 * unload if they have already been previously loaded.
	 * @param string $obj Object to remove.
	 */
	public function del(string $obj): void
	{
		$obj = ltrim($obj, '\\');
		$this->data->del($obj);
	}
}
