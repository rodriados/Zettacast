<?php
/**
 * Zettacast helper functions file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
use Zettacast\Zettacast;

if(!function_exists('dd')) {
	/**
	 * Dumps the passed variables and ends execution.
	 * @param array ...$vars Variables to be dumped.
	 */
	function dd(...$vars) {
		
		var_dump(...$vars);
		exit;
		
	}
}

if(!function_exists('e')) {
	/**
	 * Escapes all HTML entities in the given string.
	 * @param string $str String to be escaped.
	 * @return string Escaped string.
	 */
	function e($str) {
		
		return htmlentities($str, ENT_QUOTES, 'UTF-8', false);
		
	}
}

if(!function_exists('with')) {
	/**
	 * Returns given object. This is useful for method chaining.
	 * @param mixed $object Object to be returned.
	 * @return mixed Given object.
	 */
	function with($object) {
		
		return $object instanceof Closure ? $object() : $object;
		
	}
}

if(!function_exists('zetta')) {
	/**
	 * Gets the current framework instance.
	 * @param string $abstract Abstraction to be made.
	 * @return Zettacast|mixed Requested abstraction or framework instance.
	 */
	function zetta(string $abstract = null) {
		
		if(is_null($abstract))
			return Zettacast::instance();
		
		return Zettacast::instance()->make($abstract);
		
	}
}
