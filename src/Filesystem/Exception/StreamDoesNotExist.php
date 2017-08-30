<?php
/**
 * Zettacast\Filesystem\Exception\StreamDoesNotExist exception file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Filesystem\Exception;

use Exception;

class StreamDoesNotExist
	extends Exception
{
	public function __construct(string $path) {
		parent::__construct($path);
	}
}
