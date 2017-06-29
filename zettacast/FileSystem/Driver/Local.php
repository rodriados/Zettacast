<?php
/**
 * Zettacast\FileSystem\Driver\Local class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\FileSystem\Driver;

use Exception;
use Zettacast\FileSystem\Info;
use Zettacast\FileSystem\File;
use Zettacast\FileSystem\Contract\Driver;
use Zettacast\FileSystem\Contract\Handler;

class Local implements Driver
{
	protected $prefix;
	
	public function __construct(string $root = DOCROOT)
	{
		$root = is_link($root) ? realpath($root) : $root;
		$this->ensure($root);
		
		if(!is_dir($root) or !is_readable($root))
			throw new Exception('The root path '.$root.' is not readable.');
		
		$this->prefix = $root ? rtrim($root, '\\/').'/' : null;
	}
	
	public function __call(string $name, array $arguments)
	{
		$location = $this->prefix($arguments[0]);
		return $this->metadata($location, $name, $arguments[1] ?? null);
	}
	
	public function copy(string $path, string $target) : bool
	{
		$location = $this->prefix($path);
		$destiny = $this->prefix($target);
		$this->ensure($this->prefix(dirname($destiny)));
		
		return copy($location, $destiny);
	}
	
	public function has(string $path) : bool
	{
		$location = $this->prefix($path);
		return file_exists($location);
	}
	
	public function isdir(string $path) : bool
	{
		$location = $this->prefix($path);
		return is_dir($location);
	}
	
	public function isfile(string $path) : bool
	{
		$location = $this->prefix($path);
		return is_file($location);
	}
	
	public function list(string $dir = null) : array
	{
		if(!is_dir($location = $this->prefix($dir)))
			return [];
		
		return array_slice(scandir($location), 2);
	}
	
	public function metadata(string $path, string $data = null, $default = null)
	{
		$location = $this->prefix($path);
		$metadata = new Info($location);
		return $data ? $metadata->get($data, $default) : $metadata;
	}
	
	public function mkdir(string $path, int $perms = 0777) : bool
	{
		$location = $this->prefix($path);
		return (!is_dir($location) && mkdir($location, $perms, true));
	}
	
	public function open(string $filename, string $mode = 'r') : Handler
	{
		$location = $this->prefix($filename);
		return new File($location, $mode);
	}
	
	public function permission(string $path, int $perms = 0777) : bool
	{
		$location = $this->prefix($path);
		return chmod($location, $perms);
	}
	
	public function read(string $filename) : string
	{
		$location = $this->prefix($filename);
		return file_get_contents($location);
	}
	
	public function readTo(string $filename, $target)
	{
		if($target instanceof Handler)
			return $target->write($this->read($filename));
		
		return fwrite($target, $this->read($filename));
	}
	
	public function remove(string $path) : bool
	{
		$location = $this->prefix($path);
		return unlink($location);
	}
	
	public function rename(string $path, string $newpath) : bool
	{
		$location = $this->prefix($path);
		$destiny = $this->prefix($newpath);
		$this->ensure($this->prefix(dirname($destiny)));
		
		return rename($location, $destiny);
	}
	
	public function rmdir(string $path) : bool
	{
		$location = $this->prefix($path);
		$files = $this->list($path);
		
		if(!is_dir($location))
			return false;
		
		foreach($files as $file)
			if(is_dir("$location/$file"))   $this->rmdir("$path/$file");
			else                            $this->remove("$path/$file");
		
		return rmdir($location);
	}
	
	public function write(string $filename, $content, int $flags = null) : int
	{
		$location = $this->prefix($filename);
		$this->ensure($this->prefix(dirname($location)));
		
		return file_put_contents($location, $content, $flags);
	}
	
	public function writeFrom(string $filename, $source) : int
	{
		$location = $this->prefix($filename);
		$this->ensure($this->prefix(dirname($location)));
		
		return with(new File($location, 'w+b'))->writeFrom($source);
	}
	
	protected function ensure(string $path)
	{
		if(is_dir($path))
			return;
		
		$umask = umask(0);
		@mkdir($path, 0755, true);
		umask($umask);
		
		if(!is_dir($path))
			throw new Exception('Impossible to create directory "'.$path.'"');
	}
	
	protected function prefix(string $path)
	{
		return $this->prefix.ltrim($path, '\\/');
	}

	protected function unprefix(string $path)
	{
		return substr($path, strlen($this->prefix));
	}
	
}
