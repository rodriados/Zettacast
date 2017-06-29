<?php
/**
 * Zettacast\FileSystem\Contract\Driver interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\FileSystem\Contract;

use Zettacast\Collection\Contract\Collection;

interface Driver
{
	public function copy(string $path, string $target) : bool;
	
	public function has(string $path) : bool;
	
	public function isdir(string $path) : bool;
	
	public function isfile(string $path) : bool;
	
	public function list(string $dir = null) : array;
	
	public function metadata(string $path, string $data = null, $default = null);
	
	public function mkdir(string $path, int $perms = 0777) : bool;
	
	public function open(string $filename, string $mode = 'r') : Handler;
	
	public function permission(string $path, int $perms = 0777) : bool;
	
	public function read(string $filename) : string;
	
	public function readTo(string $filename, $target);
	
	public function remove(string $path) : bool;
	
	public function rename(string $path, string $newpath) : bool;

	public function rmdir(string $path) : bool;
	
	public function write(string $filename, $content) : int;
	
	public function writeFrom(string $filename, $source) : int;

}
