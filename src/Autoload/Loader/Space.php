<?php
/**
 * Zettacast\Autoload\Loader\Space class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2016 Rodrigo Siqueira
 */
namespace Zettacast\Autoload\Loader;

use Zettacast\Contract\Autoload\Loader;

/**
 * The Space loader class is responsible for implementing the loading of
 * classes in namespaces explicitly listed along the execution.
 * @package Zettacast\Autoload
 * @version 1.0
 */
class Space
	implements Loader
{
	/**
	 * Listed namespaces. The entries in this array should not override
	 * Zettacast namespaces or unexpected errors may occur.
	 * @var array Maps namespaces to their actual paths.
	 */
	protected $spaces;
	
	/**
	 * Tries to load an invoked and not yet loaded object.
	 * @param string $obj Object to be loaded.
	 * @return bool Was the object successfully loaded?
	 */
	public function load(string $obj): bool
	{
		if(empty($this->spaces))
			return false;
		
		$obj = ltrim($obj, '\\');
		$qnsname = explode('\\', $obj);
		$objname = array_pop($qnsname);
		
		while($qnsname) {
			$space = implode('\\', $qnsname);
			
			if(isset($this->spaces[$space])) {
				$fname = $this->spaces[$space].'/'.$objname.'.php';
				break;
			}
			
			$objname = array_pop($qnsname).'/'.$objname;
		}
		
		if(!isset($fname) or !file_exists($fname))
			return false;
		
		require $fname;
		return true;
	}
	
	/**
	 * Resets the loader to its initial state.
	 * @return void No return expected.
	 */
	public function reset()
	{
		$this->spaces = [];
	}
	
	/**
	 * Adds new namespace map entries. Conflicting entries will simply be
	 * overwritten to the newest value.
	 * @param array $map Map of namespaces to be added.
	 */
	public function add(array $map)
	{
		foreach($map as $sname => $spath) {
			$sname = strtolower(ltrim($sname, '\\'));
			$this->spaces[$sname] = rtrim($spath, '/');
		}
	}
	
	/**
	 * Removes an entry from the map. Classes to be loaded using this loader
	 * will not be unloaded if they have already been loaded.
	 * @param array|string $slist Namespaces to be removed.
	 */
	public function del($slist)
	{
		foreach((array)$slist as $sname) {
			$sname = strtolower(ltrim($sname, '\\'));
			
			if(isset($this->spaces[$sname]))
				unset($this->spaces[$sname]);
		}
	}
	
	/**
	 * Resets and erases all previous entries and put new ones in the list.
	 * @param array $map New namespace mappings.
	 */
	public function set(array $map)
	{
		$this->reset();
		$this->add($map);
	}
	
}
