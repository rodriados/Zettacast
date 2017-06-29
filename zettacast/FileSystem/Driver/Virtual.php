<?php
/**
 * Zettacast\FileSystem\Driver\Virtual class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\FileSystem\Driver;

use Exception;
use Zettacast\FileSystem\File;
use Zettacast\Collection\Recursive;
use Zettacast\FileSystem\Contract\Driver;
use Zettacast\FileSystem\Contract\Handler;

class Virtual implements Driver
{
	protected $storage;
	
	public function __construct()
	{
		$this->storage = new Recursive([
			'.' => ['type' => 'dir']
        ]);
	}
	
	public function __call(string $name, array $args)
	{
		return $this->metadata($args[0], $name, $args[1] ?? null);
	}
	
	public function copy(string $path, string $target) : bool
	{
		if(!$this->isfile($path) or !$this->mkdir(dirname($target)))
			return false;
		
		$this->storage->set($target, $this->storage->get($path)->all());
		return true;
	}
	
	public function has(string $path) : bool
	{
		return $this->storage->has($path);
	}
	
	public function isdir(string $path) : bool
	{
		return $this->has($path) && $this->storage->get($path)->type == 'dir';
	}
	
	public function isfile(string $path) : bool
	{
		return $this->has($path) && $this->storage->get($path)->type == 'file';
	}
	
	public function list(string $dir = null) : array
	{
		return $this->storage->keys()->filter(function ($path) use ($dir) {
			if($path === '.')
				return false;
			
			return dirname($path) === $dir;
		})->values()->all();
	}
	
	public function metadata(string $path, string $data = null, $default = null)
	{
		return $data
			? $this->storage->get($path, [])->get($data, $default)
			: $this->storage->get($path, $default);
	}
	
	public function mkdir(string $path, int $perms = 0777) : bool
	{
		if($this->isdir($path))
			return true;
		
		if($this->isfile($path) or !$this->mkdir(dirname($path)))
			return false;
		
		$this->storage->set($path, ['type' => 'dir']);
		return true;
	}
	
	public function open(string $filename, string $mode = 'r') : Handler
	{
		if(!$this->isfile($filename))
			throw new Exception($filename." is not a file!");
		
		// Mode does not make sense in a virtual filesystem, as files are
		// always open and thus can always be written to or read from.
		return $this->storage->get($filename)->stream;
	}
	
	public function permission(string $path, int $perms = 0777) : bool
	{
		// Permissions do not make sense in a virtual filesystem. As data will
		// anyway vanish as soon as the script ends, there is no reason to
		// prohibit anyone to access files contained in it.
		return true;
	}
	
	public function read(string $filename) : string
	{
		if(!$this->isfile($filename))
			throw new Exception($filename." is not a file!");
		
		return $this->storage->get($filename)->stream->read();
	}
	
	public function readTo(string $filename, $target)
	{
		if($target instanceof Handler)
			return $target->write($this->read($filename));
		
		return fwrite($target, $this->read($filename));
	}
	
	public function remove(string $path) : bool
	{
		if(!$this->isfile($path))
			return false;
		
		$this->storage->del($path);
		return true;
	}
	
	public function rename(string $path, string $newpath) : bool
	{
		if(!$this->isfile($path) or !$this->mkdir(dirname($newpath)))
			return false;
		
		$this->storage->set($newpath, $this->storage->get($path)->all());
		$this->storage->del($path);
		return true;
	}
	
	public function rmdir(string $path) : bool
	{
		if(!$this->isdir($path))
			return false;
		
		foreach($this->list($path) as $target)
			if($this->isdir($target))   $this->rmdir($target);
			else                        $this->remove($target);
		
		$this->storage->del($path);
		return true;
	}
	
	public function write(string $filename, $content, int $flags = null) : int
	{
		if(!$this->mkdir(dirname($filename)))
			return false;
		
		$this->storage->set($filename, [
			'timestamp' => time(),
			'stream' => $file = File::temp(),
			'mime' => with(new \finfo(FILEINFO_MIME_TYPE))->buffer($content),
		    'type' => 'file',
		]);
		
		$size = $file->write($content);
		$file->rewind();
		return $size;
	}
	
	public function writeFrom(string $filename, $source) : int
	{
		if($source instanceof Handler)
			return $this->write($filename, $source->read());
		
		return $this->write($filename, stream_get_contents($source));
	}
	
}
