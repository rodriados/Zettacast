<?php
/**
 * Zettacast\Filesystem\Disk\VirtualDisk class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Filesystem\Disk;

use Zettacast\Filesystem\File;
use Zettacast\Collection\Sequence;
use Zettacast\Stream\StreamInterface;
use Zettacast\Filesystem\DiskInterface;
use Zettacast\Collection\RecursiveCollection;
use Zettacast\Collection\SequenceInterface;

/**
 * Emulates a disk for virtual files. All of the contents saved in this disk
 * will be erased as soon as this object is destructed.
 * @package Zettacast\Filesystem
 * @version 1.0
 */
class VirtualDisk implements DiskInterface
{
	/**
	 * This collection is our disk. All content will be stored in here.
	 * @var RecursiveCollection Driver disk. Holds all driver data.
	 */
	protected $storage;
	
	/**
	 * VirtualDisk constructor.
	 * Creates our root directory so we have a base where we can put our files.
	 */
	public function __construct()
	{
		$this->storage = new RecursiveCollection([
			'.' => [
				'timestamp' => time(),
				'type' => 'dir'
			]
	    ]);
	}
	
	/**
	 * Creates a copy of a file in given destiny path.
	 * @param string $path File to copy.
	 * @param string $target Path to which copy is created.
	 * @return bool Was it possible to copy such a file?
	 */
	public function copy(string $path, string $target): bool
	{
		$src = $this->treat($path);
		$tgt = $this->treat($target);
		
		if(!$this->isfile($src) or !$this->mkdir(dirname($tgt)))
			return false;
		
		$this->storage->set($tgt, $this->storage->get($src)->all());
		return true;
	}
	
	/**
	 * Checks whether a path exists in driver.
	 * @param string $path Path to check existance.
	 * @return bool Was the path found?
	 */
	public function has(string $path): bool
	{
		$tgt = $this->treat($path);
		return $this->storage->has($tgt);
	}
	
	/**
	 * Returns metadata available for given path.
	 * @param string $path Target path for metadata request.
	 * @param string $data Specific data to retrieve.
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
	 * Checks whether given path is a directory.
	 * @param string $path Path to check.
	 * @return bool Is path a directory?
	 */
	public function isdir(string $path): bool
	{
		$tgt = $this->treat($path);
		
		return $this->has($tgt)
			&& $this->storage->get($tgt)->type == 'dir';
	}
	
	/**
	 * Checks whether given path is a file.
	 * @param string $path Path to check.
	 * @return bool Is path a file?
	 */
	public function isfile(string $path): bool
	{
		$tgt = $this->treat($path);
		
		return $this->has($tgt)
		    && $this->storage->get($tgt)->type == 'file';
	}
	
	/**
	 * Lists all files and directories contained in given path.
	 * @param string $dir Path to list.
	 * @return SequenceInterface All directory contents in the path.
	 */
	public function list(string $dir = null): SequenceInterface
	{
		$tgt = $this->treat($dir);
		
		return with(new Sequence($this->storage->keys()))
			->filter(function($path) use($tgt) {
				return $path !== '.' && dirname($path) === $tgt;
			});
	}
	
	/**
	 * Creates a new directory into driver.
	 * @param string $path Path of directory to create.
	 * @return bool Was directory successfully created?
	 */
	public function mkdir(string $path): bool
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
	public function move(string $path, string $newpath): bool
	{
		$src = $this->treat($path);
		$tgt = $this->treat($newpath);
		
		if(!$this->isfile($src) or !$this->mkdir(dirname($tgt)))
			return false;
		
		$this->storage->set($tgt, $this->storage->get($src)->all());
		$this->storage->del($src);
		return true;
	}
	
	/**
	 * Opens a file as a directly editable stream.
	 * @param string $filename File to open.
	 * @param string $mode Reading/writing mode the file should open in.
	 * @return StreamInterface The directly editable file handler.
	 */
	public function open(string $filename, string $mode = 'r'): StreamInterface
	{
		$src = $this->treat($filename);

		return $this->isfile($src)
			? $this->storage->get($src)->stream
			: null;
	}
	
	/**
	 * Retrieves all contents from given file.
	 * @param string $filename File to read.
	 * @return string All file contents.
	 */
	public function read(string $filename)
	{
		$src = $this->treat($filename);

		if(!$this->isfile($src))
			return null;
		
		/* @var StreamInterface $stream */
		$stream = $this->storage->get($src)->stream;
		$offset = $stream->tell();

		$stream->rewind();
		$content = $stream->read();
		$stream->seek($offset);
		
		return $content;
	}
	
	/**
	 * Retrieves contents from a file and puts it into a stream.
	 * @param string $file Source file to read.
	 * @param resource|StreamInterface $stream Target stream to put content on.
	 * @param int $length Maximum number of bytes to write to stream.
	 * @return int Length of data read from file.
	 */
	public function readto(string $file, $stream, int $length = null): int
	{
		$fcontent = $this->read($file);
		
		return $stream instanceof StreamInterface
			? $stream->write($fcontent, $length)
			: fwrite($stream, $fcontent, $length ?? strlen($fcontent));
	}
	
	/**
	 * Removes a file or directory from driver.
	 * @param string $path Path to file to from driver.
	 * @return bool Was file or directory successfully removed?
	 */
	public function remove(string $path): bool
	{
		$tgt = $this->treat($path);

		if(!$this->isfile($tgt))
			return false;
		
		$this->storage->del($tgt);
		return true;
	}
	
	/**
	 * Removes a directory from driver.
	 * @param string $path Path to directory to remove from driver.
	 * @return bool Was directory successfully removed?
	 */
	public function rmdir(string $path): bool
	{
		$tgt = $this->treat($path);
		
		if(!$this->isdir($tgt))
			return false;
		
		foreach($this->list($tgt) as $target)
			$this->isdir($target)
				? $this->rmdir($target)
				: $this->remove($target);
		
		$this->storage->del($tgt);
		return true;
	}
	
	/**
	 * Appends the content to a file, that will be created if needed.
	 * @param string $filename Target file path to write.
	 * @param mixed $content Content to write to path.
	 * @return int Number of written characters.
	 */
	public function update(string $filename, $content): int
	{
		$tgt = $this->treat($filename);
		
		if(!$this->storage->has($tgt))
			return $this->write($tgt, $content);
		
		/* @var StreamInterface $stream */
		$stream = $this->storage->get($tgt)->stream;
		$offset = $stream->tell();
		
		$stream->forward();
		$stream->write($content);
		$stream->seek($offset);
		
		return strlen($content);
	}
	
	/**
	 * Retrieves content from stream and appends it to a file.
	 * @param resource|StreamInterface $stream Source content is retrieved from.
	 * @param string $file Target file to write to.
	 * @param int $length Maximum number of bytes to write to file.
	 * @return int Total length of data written to file.
	 */
	public function updatefrom($stream, string $file, int $length = null): int
	{
		return $stream instanceof StreamInterface
			? $this->update($file, $stream->read($length))
			: $this->update($file, stream_get_contents($stream, $length));
	}
	
	/**
	 * Writes the content to a file, that will be created if needed.
	 * @param string $filename Target file path to write.
	 * @param mixed $content Content to write to path.
	 * @return int Number of written characters.
	 */
	public function write(string $filename, $content): int
	{
		$tgt = $this->treat($filename);
		
		if(!$this->mkdir(dirname($tgt)))
			return false;
		
		$this->storage->set($tgt, [
			'timestamp' => time(),
		    'stream' => $file = File::virtual($content),
		    'mime' => with(new \finfo(FILEINFO_MIME_TYPE))->buffer($content),
		    'type' => 'file',
		]);
		
		return strlen($content);
	}
	
	/**
	 * Retrieves content from stream and writes it to a file.
	 * @param resource|StreamInterface $stream Stream content is retrieved from.
	 * @param string $file Target file to write to.
	 * @param int $length Maximum number of bytes to write to file.
	 * @return int Total length of data written to file.
	 */
	public function writefrom($stream, string $file, int $length = null): int
	{
		return $stream instanceof StreamInterface
			? $this->write($file, $stream->read($length))
			: $this->write($file, stream_get_contents($stream, $length));
	}
	
	/**
	 * Treats the virtual paths, so all of them keep a uniform formatation.
	 * @param string $path Path to treat.
	 * @return string Trated path.
	 */
	protected function treat(string $path = null): string
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
