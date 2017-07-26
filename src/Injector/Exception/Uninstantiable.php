<?php
/**
 * Zettacast\Injector\Exception\Uninstantiable exception file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Injector\Exception;

use Exception;

class Uninstantiable
	extends Exception
{
	public function __construct(string $concrete) {
		parent::__construct($concrete);
	}
}
