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

class ObjectLoader implements LoaderInterface
{
	/**
	 * Listed objects. The entries in this collection should not override
	 * Zettacast classes or unexpected errors may occur.
	 * @var Collection Mappings of objects to their actual paths.
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
	 * @inheritdoc
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
	 * Register a new object file.
	 * @param string $obj Object name to register.
	 * @param string $file File in which object can be found.
	 */
	public function set(string $obj, string $file): void
	{
		$obj = ltrim($obj, '\\');
		$this->data->set($obj, $file);
	}
	
	/**
	 * Remove an entry from the map. Classes to load using this loader will not
	 * unload if they have already been loaded.
	 * @param string $obj Object remove.
	 */
	public function del(string $obj): void
	{
		$obj = ltrim($obj, '\\');
		$this->data->del($obj);
	}
}
