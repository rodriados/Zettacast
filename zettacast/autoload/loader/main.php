<?php
/**
 * Autoload\Loader\Initial class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2016 Rodrigo Siqueira
 */
namespace Zettacast\Autoload\Loader;

/*
 * Declaration of exterior resources needed. We declare the resources we are
 * going to need so we can skip all namespace bureaucracy.
 */
use Zettacast\Autoload\Loader;

/**
 * The Initial loader class is responsible for loading all classes required by
 * the framework or the application itself. It also lets you set explicit paths
 * for classes to be loaded from.
 * @package Zettacast\Autoload
 * @version 1.0
 */
final class Main implements Loader {
	
	/**
	 * Maps classes to files. All entries in this array must be valid,
	 * otherwise, errors may occur wherever these classes are invoked.
	 * @var array Maps classes to their files.
	 */
	private $classes;
	
	/**
	 * Maps namespaces to directories. These entries are used to resolve
	 * namespaced classes requests, mapping their namespaces to a directory
	 * containing the class file. All entries in this array must be valid,
	 * otherwise, errors may occur wherever a namespaced class is invoked.
	 * @var array Maps namespaces to their directories.
	 */
	private $namespaces;
	
	/**
	 * Framework classes' aliases. The values in this array should not be
	 * changed in any circunstances as they directly affect how the framework
	 * loads it's classes.
	 * @var array
	 */
	private const fclasses = [
		
	];
	
	/**
	 * Initial loader constructor. Initializes the class and set values to
	 * instance properties.
	 */
	public function __construct() {
		
		$this->reset();
		
	}
	
	/**
	 * Tries to load an invoked and not yet loaded class. The lookup for
	 * classes happens in the framework core first and then in the explicitly
	 * indicated paths for classes and namespaces.
	 * @param string $class Class to be loaded.
	 * @return bool Was the class successfully loaded?
	 */
	public function load(string $class) : bool {

		$elem = explode('\\', ltrim($class, '\\'));
		
		return
			$this->loadFwork($elem) or
			$this->loadClass($elem) or
			$this->loadNamespace($elem)
		;
			
	}
	
	/**
	 * Resets the loader to its initial state.
	 */
	public function reset() {
		
		$this->classes = [];
		$this->namespaces = [];
		
	}
	
	/**
	 * Adds new map entries to the map of classes. Conflicting entries will
	 * be simply overwritten to the newest value.
	 * @param array $map Map of classes to be added.
	 */
	public function addClass(array $map) {
		
		foreach($map as $cname => $cpath)
			$this->classes[ltrim($cname, '\\')] = $cpath;
		
	}
	
	/**
	 * Adds new map entries to the map of namespaces. Conflicting entries
	 * will simply be overwritten to the newest value.
	 * @param array $map Map of namespaces to be added.
	 */
	public function addNamespace(array $map) {
		
		foreach($map as $nname => $npath)
			$this->namespaces[ltrim($nname, '\\')] = rtrim($npath, '/');
		
	}
	
	/**
	 * Removes a class from the map of classes. The target class will not be
	 * unloaded in case it has already been loaded, but it will not be able to
	 * load in case it still hasn't.
	 * @param array|string $class Class to removed from map of classes.
	 */
	public function delClass(array $class) {
		
		foreach($class as $cname)
			if(isset($this->classes[$cname]))
				unset($this->classes[$cname]);
		
	}
	
	/**
	 * Removes a namespace from the map of namespaces. Classes in the target
	 * namespace will not be unloaded in case they have already been loaded,
	 * but they will not be able to load in case they still haven't.
	 * @param array|string $nspace Namespace to be removed from map.
	 */
	public function delNamespace(array $nspace) {
		
		foreach($nspace as $nname)
			if(isset($this->namespaces[$nname]))
				unset($this->namespaces[$nname]);
		
	}
	
	/**
	 * Checks if the invoked object is an internal class of the framework. If
	 * confirmed, the class is loaded and aliased.
	 * @param array $elem Namespace-exploded class name.
	 * @return bool Was the class successfully loaded?
	 */
	private function loadFwork($elem) {
		
		if(count($elem) == 1) /* proxies */ {
			
			$cname = $elem[0];
			$lower = strtolower($cname);
			
			$fname = in_array($cname, self::fclasses)
				? FWORKPATH.'/'.self::fclasses[$cname]
				: FWORKPATH."/{$lower}/{$lower}.php";
			
		} elseif($elem[0] == ZETTACAST) /* internal framework use */ {
			
			array_shift($elem);
			$lower = strtolower(implode('/', $elem));
			$fname = FWORKPATH."/{$lower}.php";
			
		}
		
		if(!isset($fname) or !file_exists($fname))
			return false;
		
		require $fname;
		
		if(isset($cname))
			class_alias('Zettacast\\'.$cname, $cname);
		
		return true;
		
	}
	
	/**
	 * Checks whether the invoked object is listed in the class loader array.
	 * If confirmed, the class is loaded directly from the list.
	 * @param array $elem Namespace-exploded class name.
	 * @return bool Was the class successfully loaded?
	 */
	private function loadClass($elem) {
		
		$cname = implode('\\', $elem);
		$fname = str_replace(['_', '\\'], '/', strtolower($cname));
		$fname = $this->classes[$cname] ?? DOCROOT."/{$fname}.php";
		
		if(!file_exists($fname))
			return false;
		
		require $fname;
		return true;
		
	}
	
	/**
	 * Checks whether the invoked object can be found inside an element of
	 * namespaces list. If confirmed, the class is loaded.
	 * @param array $elem Namespace-exploded class name.
	 * @return bool Was the class successfully loaded?
	 */
	private function loadNamespace($elem) {
		
		if(count($elem) < 2 or empty($this->namespaces))
			return false;
		
		$cname = implode('\\', $elem);
		
		foreach($this->namespaces as $nname => $npath)
			if(stripos($cname, $nname) === 0) {
			
				$fname = str_ireplace($nname, $npath, strtolower($cname));
				$fname = str_replace(['\\', '_'], '/', $fname).'.php';
				break;
			
			}
		
		if(!isset($fname) or !file_exists($fname))
			return false;
		
		require $fname;
		return true;
			
	}
	
}
