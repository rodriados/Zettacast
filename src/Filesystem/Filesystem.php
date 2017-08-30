<?php
/**
 * Zettacast\Filesystem\Filesystem class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Filesystem;

use Zettacast\Collection\Sequence;
use Zettacast\Contract\Filesystem\Driver;
use Zettacast\Contract\Filesystem\Stream as StreamContract;
use Zettacast\Contract\Filesystem\Filesystem as FilesystemContract;

/**
 * This class is responsible for interacting with a filesystem driver.
 * @package Zettacast\Filesystem
 * @version 1.0
 */
class Filesystem
	implements FilesystemContract
{
	/**
	 * The filesystem driver, responsible for manipulating the driver files.
	 * @var Driver Interfaces all interactions to the filesystem files.
	 */
	protected $driver;
	
	/**
	 * Filesystem constructor.
	 * @param Driver $driver Driver to be used in filesystem.
	 */
	public function __construct(Driver $driver)
	{
		$this->driver = $driver;
	}
	
	/**
	 * Retrieves metadata about a file or directory from driver.
	 * @param string $name Name of metadata being retrieved.
	 * @param array $args File path to be informed about.
	 * @return mixed Retrieved metadata about path, or default return value.
	 */
	public function __call(string $name, array $args)
	{
		return $this->driver->$name(...$args);
	}
	
	/**
	 * Creates a copy of a file in the given destiny path.
	 * @param string $path File to be copied.
	 * @param string $target Path to which copy is created.
	 * @return bool Was it possible to copy such a file?
	 */
	public function copy(string $path, string $target) : bool
	{
		return $this->driver->copy($path, $target);
	}
	
	/**
	 * Checks whether a path exists in the driver.
	 * @param string $path Path to be checked.
	 * @return bool Was the path found?
	 */
	public function has(string $path) : bool
	{
		return $this->driver->has($path);
	}
	
	/**
	 * Returns all metadata available for given path.
	 * @param string $path Target path for metadata request.
	 * @param string $data Specific data to be retrieved.
	 * @return mixed All metadata values or retrieved specific data.
	 */
	public function info(string $path = null, string $data = null)
	{
		return $this->driver->info($path, $data);
	}
	
	/**
	 * Checks whether the given path is a directory.
	 * @param string $path Path to be checked.
	 * @return bool Is path a directory?
	 */
	public function isdir(string $path) : bool
	{
		return $this->driver->isdir($path);
	}
	
	/**
	 * Checks whether the given path is a file.
	 * @param string $path Path to be checked.
	 * @return bool Is path a file?
	 */
	public function isfile(string $path) : bool
	{
		return $this->driver->isfile($path);
	}
	
	/**
	 * Lists all files and directories contained in the given path.
	 * @param string $dir Path to be listed.
	 * @return Sequence All directory contents in the path.
	 */
	public function list(string $dir = null) : Sequence
	{
		return $this->driver->list($dir);
	}
	
	/**
	 * Creates a new directory into the driver.
	 * @param string $path Path of the directory to be created.
	 * @return bool Was the directory successfully created?
	 */
	public function mkdir(string $path) : bool
	{
		return $this->driver->mkdir($path);
	}
	
	/**
	 * Moves given file or directory to another location.
	 * @param string $path Target path, that will be moved.
	 * @param string $newpath The new name for target file or directory.
	 * @return bool Was the file or directory successfully moved?
	 */
	public function move(string $path, string $newpath) : bool
	{
		return $this->driver->move($path, $newpath);
	}
	
	/**
	 * Opens a file as a directly editable stream.
	 * @param string $filename File to be opened.
	 * @param string $mode Reading/writing mode the file should be opened in.
	 * @return StreamContract The directly editable file handler.
	 */
	public function open(string $filename, string $mode = 'r') : StreamContract
	{
		return $this->driver->open($filename, $mode);
	}
	
	/**
	 * Retrieves all contents from the given file.
	 * @param string $filename File to be read.
	 * @return string All file contents.
	 */
	public function read(string $filename) : string
	{
		return $this->driver->read($filename);
	}
	
	/**
	 * Retrieves contents from a file and puts it into a stream.
	 * @param string $file Source file to be read.
	 * @param resource|StreamContract $stream Target stream to put content on.
	 * @param int $length Maximum number of bytes to be written to stream.
	 * @return int Length of data read from file.
	 */
	public function readTo(string $file, $stream, int $length = null) : int
	{
		return $stream instanceof StreamContract
			? $stream->write($this->read($file), $length)
			: fwrite($stream, $this->read($file), $length);
	}
	
	/**
	 * Removes a file from driver.
	 * @param string $path Path to file to be removed from driver.
	 * @return bool Was file or directory successfully removed?
	 */
	public function remove(string $path) : bool
	{
		return $this->driver->remove($path);
	}
	
	/**
	 * Removes a directory from driver.
	 * @param string $path Path to directory to be removed from driver.
	 * @return bool Was directory successfully removed?
	 */
	public function rmdir(string $path) : bool
	{
		return $this->driver->rmdir($path);
	}
	
	/**
	 * Appends the content to a file, that will be created if needed.
	 * @param string $filename Target file path to be written.
	 * @param mixed $content Content to be written to path.
	 * @return int Number of written characters.
	 */
	public function update(string $filename, $content) : int
	{
		return $this->driver->update($filename, $content);
	}
	
	/**
	 * Retrieves content from stream and appends it to a file.
	 * @param resource|StreamContract $stream Source content is retrieved from.
	 * @param string $file Target file to be written to.
	 * @param int $length Maximum number of bytes to be written to file.
	 * @return int Total length of data written to file.
	 */
	public function updateFrom($stream, string $file, int $length = null) : int
	{
		return $stream instanceof StreamContract
			? $this->update($file, $stream->read($length))
			: $this->update($file, stream_get_contents($stream, $length));
	}
	
	/**
	 * Writes the content to a file, that will be created if needed.
	 * @param string $filename Target file path to be written.
	 * @param mixed $content Content to be written to path.
	 * @return int Number of written characters.
	 */
	public function write(string $filename, $content) : int
	{
		return $this->driver->write($filename, $content);
	}
	
	/**
	 * Retrieves content from stream and writes it to a file.
	 * @param resource|StreamContract $stream Stream content is retrieved from.
	 * @param string $file Target file to be written to.
	 * @param int $length Maximum number of bytes to be written to file.
	 * @return int Total length of data written to file.
	 */
	public function writeFrom($stream, string $file, int $length = null) : int
	{
		return $stream instanceof StreamContract
			? $this->write($file, $stream->read($length))
			: $this->write($file, stream_get_contents($stream, $length));
	}
	
}
