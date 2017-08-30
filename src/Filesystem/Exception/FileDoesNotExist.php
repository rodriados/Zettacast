<?php
/**
 * Zettacast\Filesystem\Exception\FileDoesNotExist exception file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Filesystem\Exception;

use Exception;

class FileDoesNotExist
	extends Exception
{
	public function __construct(string $file) {
		parent::__construct($file);
	}
}
