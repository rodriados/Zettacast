<?php
/**
 * Zettacast\Autoload\Loader\NamespaceLoader class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Autoload\Loader;

use Zettacast\Collection\Collection;
use Zettacast\Autoload\LoaderInterface;

class NamespaceLoader implements LoaderInterface
{
	/**
	 * Listed namespaces. The entries in this collection should not override
	 * Zettacast namespaces or unexpected errors may occur.
	 * @var Collection Maps namespaces to their actual paths.
	 */
	protected $data;
	
	/**
	 * NamespaceLoader constructor.
	 * This constructor simply sets all of its properties to empty collections.
	 * @param array $data Initial namespace bindings.
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
		
		$name = explode('\\', ltrim($obj, '\\'));
		$objfile = array_pop($name).'.php';
		
		while($name && !$this->data->has($space = implode('\\', $name)))
			$objfile = array_pop($name).'/'.$objfile;
		
		if($name && isset($space))
			$fname = $this->data->get($space).'/'.$objfile;
		
		if(isset($fname) && $loaded = file_exists($fname))
			require $fname;
		
		return $loaded ?? false;
	}
	
	/**
	 * Register a new namespace folder.
	 * @param string $space Namespace to register.
	 * @param string $folder Folder containing namespace's objects.
	 */
	public function set(string $space, string $folder): void
	{
		$space = ltrim($space, '\\');
		$folder = rtrim($folder, '/');
		$this->data->set($space, $folder);
	}
	
	/**
	 * Remove an entry from map. Classes to load using this loader will not
	 * unload if they have already been loaded.
	 * @param string $space Namespace to remove.
	 */
	public function del(string $space): void
	{
		$space = ltrim($space, '\\');
		$this->data->del($space);
	}
}
