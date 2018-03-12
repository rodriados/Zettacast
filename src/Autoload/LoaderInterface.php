<?php
/**
 * Zettacast\Autoload\LoaderInterface interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Autoload;

interface LoaderInterface
{
	/**
	 * Tries to load an invoked and not yet loaded class.
	 * @param string $name Object name to be loaded.
	 * @return bool Was the class successfully loaded?
	 */
	public function load(string $name): bool;
}
