<?php
/**
 * Collection façade file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast;

use Zettacast\Collection\Dot;
use Zettacast\Collection\ReadOnly;
use Zettacast\Collection\Recursive;
use Zettacast\Collection\Simple as baseclass;
use Zettacast\Helper\Contract\Extendable;

/**
 * The Collection façade is a factory for all other collection types. This
 * class can be instanciated as a default collection but can also be used to
 * instanciate other collection types.
 * @version 1.0
 */
final class Collection {
	
	/*
	 * Extendable contract inclusion. This allows different types of collection
	 * to be created and registered during execution time.
	 */
	use Extendable;
	
	/**
	 * Builds a new collection based on a function.
	 * @param array $data Initial data to be fed to function.
	 * @param callable $fn Function to build collection. Params: key, value.
	 * @return baseclass New collection.
	 */
	public static function build($data, callable $fn) {
		$collection = self::make();

		foreach($data as $key => $value) {
			list($_key, $_value) = $fn($key, $value);
			$collection[$_key] = $_value;
		}
		
		return $collection;
		
	}
	
	/**
	 * Simple collection factory method.
	 * @param array $data Data to be set in collection.
	 * @return baseclass New collection instance.
	 */
	public static function make($data = []) {
		
		return new baseclass($data);
		
	}
	
	/**
	 * Recursive dot collection factory method.
	 * @param array $data Data to be set in collection.
	 * @return Dot New collection instance.
	 */
	public static function dot($data = []) {
		
		return new Dot($data);
		
	}
	
	/**
	 * ReadOnly collection factory method.
	 * @param array $data Data to be set in collection.
	 * @return ReadOnly New collection instance.
	 */
	public static function readonly($data = []) {
		
		return new ReadOnly($data);
		
	}
	
	/**
	 * Recursive collection factory method.
	 * @param array $data Data to be set in collection.
	 * @return Recursive New collection instance.
	 */
	public static function recursive($data = []) {
		
		return new Recursive($data);
		
	}
	
}
