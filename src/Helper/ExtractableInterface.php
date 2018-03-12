<?php
/**
 * Zettacast\Helper\ExtractableInterface interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Helper;

interface ExtractableInterface
{
	/**
	 * Gives access to object's raw contents. That is, it exposes the internal
	 * content that is wrapped by the object.
	 * @return mixed The raw object contents.
	 */
	public function raw();
}
