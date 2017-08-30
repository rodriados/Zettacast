<?php
/**
 * Zettacast\Filesystem\Driver\Virtual class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Filesystem\Driver;

use Zettacast\Collection\Sequence;
use Zettacast\Filesystem\Stream\Stream;
use Zettacast\Collection\Recursive as Collection;
use Zettacast\Filesystem\Stream\Virtual as VirtualStream;
use Zettacast\Contract\Filesystem\Stream as StreamContract;
use Zettacast\Contract\Filesystem\Driver as DriverContract;

/**
 * Emulates a driver for virtual files. All of the contents saved in this
 * driver will be erased as soon as this object is destructed.
 * @package Zettacast\Filesystem
 * @version 1.0
 */
class Virtual
	implements DriverContract
{
	/**
	 * This Collection is our disk. All content will be stored in here.
	 * @var Collection Driver disk. Holds all driver data.
	 */
	protected $storage;
	
	/**
	 * Virtual driver constructor. This constructor simply creates our root
	 * directory so we have a base where we can put our files on.
	 */
	public function __construct()
	{
		$this->storage = new Collection([
			'.' => [
				'timestamp' => time(),
				'type' => 'dir'
			]
	    ]);
	}
	
	/**
	 * Retrieves metadata about a file or directory from driver.
	 * @param string $name Name of metadata being retrieved.
	 * @param array $args File path to be informed about.
	 * @return mixed Retrieved metadata about path, or default return value.
	 */
	public function __call(string $name, array $args)
	{
		return $this->info($args[0] ?? '.', $name);
	}
	
	/**
	 * Creates a copy of a file in the given destiny path.
	 * @param string $path File to be copied.
	 * @param string $target Path to which copy is created.
	 * @return bool Was it possible to copy such a file?
	 */
	public function copy(string $path, string $target) : bool
	{
		$src = $this->treat($path);
		$tgt = $this->treat($target);
		
		if(!$this->isfile($src) or !$this->mkdir(dirname($tgt)))
			return false;
		
		$this->storage->set($tgt, $this->storage->get($src)->all());
		return true;
	}
	
	/**
	 * Checks whether a path exists in the driver.
	 * @param string $path Path to be checked.
	 * @return bool Was the path found?
	 */
	public function has(string $path) : bool
	{
		$tgt = $this->treat($path);
		return $this->storage->has($tgt);
	}
	
	/**
	 * Returns all metadata available for given path.
	 * @param string $path Target path for metadata request.
	 * @param string $data Specific data to be retrieved.
	 * @return mixed All metadata values or retrieved specific data.
	 */
	public function info(string $path = null, string $data = null)
	{
		$tgt = $this->treat($path);
		
		return !is_null($data)
			? $this->has($tgt)
				? $this->storage->get($tgt)->get($data)
				: null
			: $this->storage->get($tgt);
	}
	
	/**
	 * Checks whether the given path is a directory.
	 * @param string $path Path to be checked.
	 * @return bool Is path a directory?
	 */
	public function isdir(string $path) : bool
	{
		$tgt = $this->treat($path);
		
		return $this->has($tgt)
			&& $this->storage->get($tgt)->type == 'dir';
	}
	
	/**
	 * Checks whether the given path is a file.
	 * @param string $path Path to be checked.
	 * @return bool Is path a file?
	 */
	public function isfile(string $path) : bool
	{
		$tgt = $this->treat($path);
		
		return $this->has($tgt)
		    && $this->storage->get($tgt)->type == 'file';
	}
	
	/**
	 * Lists all files and directories contained in the given path.
	 * @param string $dir Path to be listed.
	 * @return Sequence All directory contents in the path.
	 */
	public function list(string $dir = null) : Sequence
	{
		$tgt = $this->treat($dir);
		
		return with(new Sequence($this->storage->keys()))
			->filter(function($path) use($tgt) {
				return $path !== '.' && dirname($path) === $tgt;
			});
	}
	
	/**
	 * Creates a new directory into the driver.
	 * @param string $path Path of the directory to be created.
	 * @param int $perms Permission to be given to the new directory.
	 * @return bool Was the directory successfully created?
	 */
	public function mkdir(string $path, int $perms = 0777) : bool
	{
		$tgt = $this->treat($path);
		
		if($this->isdir($tgt))
			return true;
		
		if($this->isfile($tgt) or !$this->mkdir(dirname($tgt)))
			return false;
		
		$this->storage->set($tgt, [
			'timestamp' => time(),
			'type' => 'dir'
		]);

		return true;
	}
	
	/**
	 * Moves given file or directory to another location.
	 * @param string $path Target path, that will be moved.
	 * @param string $newpath The new name for target file or directory.
	 * @return bool Was the file or directory successfully moved?
	 */
	public function move(string $path, string $newpath) : bool
	{
		$src = $this->treat($path);
		$tgt = $this->treat($newpath);
		
		if(!$this->isfile($src) or !$this->mkdir(dirname($tgt)))
			return false;
		
		$this->storage->set($tgt, $this->storage->get($src)->all());
		$this->storage->remove($src);
		return true;
	}
	
	/**
	 * Opens a file as a directly editable stream.
	 * @param string $filename File to be opened.
	 * @param string $mode Reading/writing mode the file should be opened in.
	 * @return StreamContract The directly editable file handler.
	 */
	public function open(string $filename, string $mode = 'r') : StreamContract
	{
		$src = $this->treat($filename);

		return $this->isfile($src)
			? $this->storage->get($src)->stream
			: null;
	}
	
	/**
	 * Retrieves all contents from the given file.
	 * @param string $filename File to be read.
	 * @return string All file contents.
	 */
	public function read(string $filename) : string
	{
		$src = $this->treat($filename);

		if(!$this->isfile($src))
			return (string)null;
		
		/* @var Stream $stream */
		$stream = $this->storage->get($src)->stream;
		$offset = $stream->tell();

		$stream->rewind();
		$content = $stream->read();
		$stream->seek($offset);
		
		return $content;
	}
	
	/**
	 * Removes a file or directory from driver.
	 * @param string $path Path to file to be removed from driver.
	 * @return bool Was file or directory successfully removed?
	 */
	public function remove(string $path) : bool
	{
		$tgt = $this->treat($path);

		if(!$this->isfile($tgt))
			return false;
		
		$this->storage->remove($tgt);
		return true;
	}
	
	/**
	 * Removes a directory from driver.
	 * @param string $path Path to directory to be removed from driver.
	 * @return bool Was directory successfully removed?
	 */
	public function rmdir(string $path) : bool
	{
		$tgt = $this->treat($path);
		
		if(!$this->isdir($tgt))
			return false;
		
		foreach($this->list($tgt) as $target)
			$this->isdir($target)
				? $this->rmdir($target)
				: $this->remove($target);
		
		$this->storage->remove($tgt);
		return true;
	}
	
	/**
	 * Appends the content to a file, that will be created if needed.
	 * @param string $filename Target file path to be written.
	 * @param mixed $content Content to be written to path.
	 * @return int Number of written characters.
	 */
	public function update(string $filename, $content) : int
	{
		$tgt = $this->treat($filename);
		
		if(!$this->storage->has($tgt))
			return $this->write($tgt, $content);
		
		/* @var Stream $stream */
		$stream = $this->storage->get($tgt)->stream;
		$offset = $stream->tell();
		
		$stream->forward();
		$stream->write($content);
		$stream->seek($offset);
		
		return strlen($content);
	}
	
	/**
	 * Writes the content to a file, that will be created if needed.
	 * @param string $filename Target file path to be written.
	 * @param mixed $content Content to be written to path.
	 * @return int Number of written characters.
	 */
	public function write(string $filename, $content) : int
	{
		$tgt = $this->treat($filename);
		
		if(!$this->mkdir(dirname($tgt)))
			return false;
		
		$this->storage->set($tgt, [
			'timestamp' => time(),
		    'stream' => $file = new VirtualStream($content),
		    'mime' => with(new \finfo(FILEINFO_MIME_TYPE))->buffer($content),
		    'type' => 'file',
		]);
		
		return strlen($content);
	}
	
	/**
	 * Treats the virtual paths, so all of them keep a given formatation.
	 * @param string $path Path to be treated.
	 * @return string Trated path.
	 */
	protected function treat(string $path = null)
	{
		$path = explode('/', $path);
		$true = [];
		
		foreach($path as $part)
			if($part == '..')
				!empty($true) && array_pop($true);
			elseif($part != '' && $part != '.')
				array_push($true, $part);
		
		return implode('/', $true) ?: '.';
	}
	
}
