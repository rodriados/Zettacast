<?php
/**
 * Zettacast\FileSystem\Contract\Handler interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\FileSystem\Contract;

interface Handler
{
	public function read(int $length = null) : string;
	
	public function readTo($target, int $length = null);
	
	public function write(string $content, int $length = null) : int;

	public function writeFrom($source, int $length = null) : int;

}
