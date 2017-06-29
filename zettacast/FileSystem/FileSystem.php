<?php
/**
 * Zettacast\FileSystem\FileSystem class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\FileSystem;

use Zettacast\FileSystem\Driver\Local;
use Zettacast\FileSystem\Driver\Virtual;
use Zettacast\FileSystem\Contract\Driver;
use Zettacast\FileSystem\Contract\Handler;

class FileSystem implements Driver
{
	protected $driver;
	
	public function __construct(Driver $driver)
	{
		$this->driver = $driver;
	}
	
	public function __call(string $name, array $arguments)
	{
		$path = self::normalize($arguments[0]);
		return $this->metadata($path, $name, $arguments[1] ?? null);
	}
	
	public function copy(string $path, string $target) : bool
	{
		$path = self::normalize($path);
		$target = self::normalize($target);
		return $this->driver->copy($path, $target);
	}
	
	public function has(string $path) : bool
	{
		$path = self::normalize($path);
		return !strlen($path) ? false : $this->driver->has($path);
	}
	
	public function hash(string $path) : string
	{
		$path = self::normalize($path);
		return md5($this->driver->read($path));
	}
	
	public function isdir(string $path) : bool
	{
		$path = self::normalize($path);
		return $this->driver->isdir($path);
	}
	
	public function isfile(string $path) : bool
	{
		$path = self::normalize($path);
		return $this->driver->isfile($path);
	}
	
	public function list(string $dir = null) : array
	{
		$dir = self::normalize($dir);
		return $this->driver->list($dir);
	}
	
	public function metadata(string $path, string $data = null, $default = null)
	{
		$path = self::normalize($path);
		return $this->driver->metadata($path, $data, $default);
	}
	
	public function mkdir(string $path, int $permision = 0777) : bool
	{
		$path = self::normalize($path);
		return $this->driver->mkdir($path, $permision);
	}
	
	public function open(string $filename, string $mode = 'r') : Handler
	{
		$filename = self::normalize($filename);
		return $this->driver->open($filename, $mode);
	}
	
	public function permission(string $path, int $permission = 0777) : bool
	{
		$path = self::normalize($path);
		return $this->driver->permission($path, $permission);
	}
	
	public function read(string $filename) : string
	{
		$filename = self::normalize($filename);
		return $this->driver->read($filename);
	}
	
	public function readTo(string $filename, $target)
	{
		$filename = self::normalize($filename);
		return $this->driver->readTo($filename, $target);
	}
	
	public function remove(string $path) : bool
	{
		$path = self::normalize($path);
		return $this->driver->remove($path);
	}
	
	public function rename(string $path, string $newpath) : bool
	{
		$path = self::normalize($path);
		$newpath = self::normalize($newpath);
		return $this->driver->rename($path, $newpath);
	}
	
	public function rmdir(string $path) : bool
	{
		$path = self::normalize($path);
		return $this->driver->rmdir($path);
	}
	
	public function write(string $filename, $content) : int
	{
		$filename = self::normalize($filename);
		return $this->driver->write($filename, $content);
	}
	
	public function writeFrom(string $filename, $source) : int
	{
		$filename = self::normalize($filename);
		return $this->driver->writeFrom($filename, $source);
	}
	
	public static function local(string $root = DOCROOT) : FileSystem
	{
		return new static(new Local($root));
	}

	public static function temp() : FileSystem
	{
		return new static(new Virtual);
	}
	
	public static function normalize(string $path)
	{
		$path = str_replace('\\', '/', $path);
		$parts = [];
		
		while(preg_match('#\p{C}+|^\./#u', $path))
			$path = preg_replace('#\p{C}+|^\./#u', '', $path);
		
		foreach(explode('/', $path) as $part)
			if($part == '' or $part == '.') continue;
			elseif($part == '..')           array_pop($parts);
			else                            $parts[] = $part;
				
		return implode('/', $parts);
	}
	
}
