<?php
/**
 * Zettacast\Contract\ExtractableInterface interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Contract;

/**
 * Extractable interface. Allows the public access to the internal content of
 * the object implementing this interface.
 * @package Zettacast\Contract
 */
interface ExtractableInterface
{
	/**
	 * Gives access to the object's raw contents. That is, it exposes the
	 * internal content that is wrapped by the object.
	 * @return mixed The raw object contents.
	 */
	public function raw();
	
}
