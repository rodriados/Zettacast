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
use Zettacast\Collection\Contract\Collection;

/**
 * Driver for virtual files. This driver emulates all operations to a virtual
 * filesystem. The content stored in this driver cannot be recovered after
 * its instance is destroyed.
 * @package Zettacast\FileSystem\Driver
 * @version 1.0
 */
class Virtual implements Driver
{
	/**
	 * This Collection is our disk. All content will be stored in here.
	 * @var Recursive Driver disk. Holds all driver data.
	 */
	protected $storage;
	
	/**
	 * Virtual driver constructor. This constructor simply creates our root
	 * directory so we have a base where we can put our files on.
	 */
	public function __construct()
	{
		$this->storage = new Recursive([
			'.' => ['type' => 'dir']
        ]);
	}
	
	/**
	 * Retrieves metadata from driver.
	 * @param string $name Name of metadata being retrieved.
	 * @param array $args File path and default value if not found.
	 * @return Collection
	 */
	public function __call(string $name, array $args)
	{
		return $this->meta($args[0], $name, $args[1] ?? null);
	}
	
	/**
	 * Creates a copy of a file in the given destiny path.
	 * @param string $path File to be copied.
	 * @param string $target Path to which copy is created.
	 * @return bool Was it possible to copy such a file?
	 */
	public function copy(string $path, string $target) : bool
	{
		if(!$this->isfile($path) or !$this->mkdir(dirname($target)))
			return false;
		
		$this->storage->set($target, $this->storage->get($path)->all());
		return true;
	}
	
	/**
	 * Checks whether a path exists in the driver.
	 * @param string $path Path to be checked.
	 * @return bool Was the path found?
	 */
	public function has(string $path) : bool
	{
		return $this->storage->has($path);
	}
	
	/**
	 * Checks whether the given path is a directory.
	 * @param string $path Path to be checked.
	 * @return bool Is path a directory?
	 */
	public function isdir(string $path) : bool
	{
		return $this->has($path) && $this->storage->get($path)->type == 'dir';
	}
	
	/**
	 * Checks whether the given path is a file.
	 * @param string $path Path to be checked.
	 * @return bool Is path a file?
	 */
	public function isfile(string $path) : bool
	{
		return $this->has($path) && $this->storage->get($path)->type == 'file';
	}
	
	/**
	 * Lists all files and directories contained in the given path.
	 * @param string $dir Path to be listed.
	 * @return array All directory contents in the path.
	 */
	public function list(string $dir = null) : array
	{
		return $this->storage->keys()->filter(function ($path) use ($dir) {
			if($path === '.')
				return false;
			
			return dirname($path) === $dir;
		})->values()->all();
	}
	
	/**
	 * Returns all metadata available for given path.
	 * @param string $path Target path for metadata request.
	 * @param string $data Specific data to be retrieved.
	 * @param mixed $default Value to return if path or metadata not found.
	 * @return mixed All metadata values or retrieved specific data.
	 */
	public function meta(
		string $path,
		string $data = null,
		$default = null
	) : Collection {
		return $data
			? $this->storage->get($path, [])->get($data, $default)
			: $this->storage->get($path, $default);
	}
	
	/**
	 * Creates a new directory into the driver.
	 * @param string $path Path of the directory to be created.
	 * @param int $perms Permission to be given to the new directory.
	 * @return bool Was the directory successfully created?
	 */
	public function mkdir(string $path, int $perms = 0777) : bool
	{
		if($this->isdir($path))
			return true;
		
		if($this->isfile($path) or !$this->mkdir(dirname($path)))
			return false;
		
		$this->storage->set($path, ['type' => 'dir']);
		return true;
	}
	
	/**
	 * Opens a file as a directly editable object.
	 * @param string $filename File to be opened.
	 * @param string $mode Reading/writing mode the file should be opened in.
	 * @return Handler The directly editable file handler.
	 * @throws Exception The path is not a file.
	 */
	public function open(string $filename, string $mode = 'r') : Handler
	{
		if(!$this->isfile($filename))
			throw new Exception($filename." is not a file!");
		
		// Mode does not make sense in a virtual filesystem, as files are
		// always open and thus can always be written to or read from.
		return $this->storage->get($filename)->stream;
	}
	
	/**
	 * Edits the permission information of the given path.
	 * @param string $path Path to be editted.
	 * @param int $perms Permission to be set to given path.
	 * @return bool Was permission successfully executed?
	 */
	public function permission(string $path, int $perms = 0777) : bool
	{
		// Permissions do not make sense in a virtual filesystem. As data will
		// anyway vanish as soon as the script ends, there is no reason to
		// prohibit anyone to access files contained in it.
		return true;
	}
	
	/**
	 * Retrieves all contents from the given file.
	 * @param string $filename File to be read.
	 * @return string All file contents.
	 * @throws Exception The path is not a file.
	 */
	public function read(string $filename) : string
	{
		if(!$this->isfile($filename))
			throw new Exception($filename." is not a file!");
		
		return $this->storage->get($filename)->stream->read();
	}
	
	/**
	 * Reads all of file contents to a stream or target file.
	 * @param string $filename File to be read.
	 * @param mixed $target Target to which file contents is put onto.
	 * @return mixed Return value is not defined.
	 */
	public function readTo(string $filename, $target)
	{
		if($target instanceof Handler)
			return $target->write($this->read($filename));
		
		return fwrite($target, $this->read($filename));
	}
	
	/**
	 * Removes a file from driver.
	 * @param string $path Path to file to be removed from driver.
	 * @return bool Was file successfully removed?
	 */
	public function remove(string $path) : bool
	{
		if(!$this->isfile($path))
			return false;
		
		$this->storage->del($path);
		return true;
	}
	
	/**
	 * Renames given file or directory and moves it, if needed.
	 * @param string $path Target path, that will be renamed.
	 * @param string $newpath The new name for target file or directory.
	 * @return bool Was the file or directory successfully renamed?
	 */
	public function rename(string $path, string $newpath) : bool
	{
		if(!$this->isfile($path) or !$this->mkdir(dirname($newpath)))
			return false;
		
		$this->storage->set($newpath, $this->storage->get($path)->all());
		$this->storage->del($path);
		return true;
	}
	
	/**
	 * Removes a directory from driver.
	 * @param string $path Path to directory to be removed from driver.
	 * @return bool Was directory successfully removed?
	 */
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
	
	/**
	 * Writes the content to a file, that will be created if needed.
	 * @param string $filename Target file path to be written.
	 * @param mixed $content Content to be written to path.
	 * @return int Number of written characters.
	 */
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
	
	/**
	 * Writes the content of a resource or File to a file located in the
	 * driver, that will be created if needed.
	 * @param string $filename Target file path to be written.
	 * @param mixed $source Source to retrieve file content's from.
	 * @return int Number of written characters.
	 */
	public function writeFrom(string $filename, $source) : int
	{
		if($source instanceof Handler)
			return $this->write($filename, $source->read());
		
		return $this->write($filename, stream_get_contents($source));
	}
	
}
