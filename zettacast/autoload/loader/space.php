<?php
/**
 * Autoload\Loader\Space class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2016 Rodrigo Siqueira
 */
namespace Zettacast\Autoload\Loader;

/**
 * The Space loader class is responsible for implementing the loading of
 * classes in namespaces explicitly listed along the execution.
 * @package Zettacast\Autoload
 * @version 1.0
 */
class Space extends Base {
	
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
	public function load(string $obj): bool {
		
		$objname = strtolower(ltrim($obj, '\\'));
		
		$sname = explode('\\', $objname);
		$objname = array_pop($sname);
		
		if(!$sname or empty($this->spaces))
			return false;

		while($sname) {
			
			$space = implode('\\', $sname);
			
			if(isset($this->spaces[$space])) {
				
				$fname = $this->spaces[$space].'/'.$objname.'.php';
				break;
				
			}
			
			$objname = array_pop($sname).'/'.$objname;
			
		}
		
		if(!isset($fname) or !file_exists($fname))
			return false;
		
		require $fname;
		return true;
		
	}
	
	/**
	 * Resets the loader to its initial state.
	 * @return self Loader instance.
	 */
	public function reset() {
		
		$this->spaces = [];
		return $this;
		
	}
	
	/**
	 * Adds new namespace map entries. Conflicting entries will simply be
	 * overwritten to the newest value.
	 * @param array $map Map of namespaces to be added.
	 * @return self Loader instance.
	 */
	public function add(array $map) {
		
		foreach($map as $sname => $spath) {
			
			$sname = strtolower(ltrim($sname, '\\'));
			$this->spaces[$sname] = rtrim($spath, '/');
			
		}
		
		return $this;
		
	}
	
	/**
	 * Removes an entry from the map. Classes to be loaded using this loader
	 * will not be unloaded if they have already been loaded.
	 * @param array|string $slist Namespaces to be removed.
	 * @return self Loader instance.
	 */
	public function del($slist) {
		
		foreach((array)$slist as $sname) {
			
			$sname = strtolower(ltrim($sname, '\\'));
			
			if(isset($this->spaces[$sname]))
				unset($this->spaces[$sname]);
			
		}
		
		return $this;
		
	}
	
	/**
	 * Resets and erases all previous entries and put new ones in the list.
	 * @param array $map New namespace mappings.
	 * @return self Loader instance.
	 */
	public function set(array $map) {
		
		$this->reset();
		$this->add($map);
		
		return $this;
		
	}
	
}
