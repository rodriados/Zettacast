<?php
/**
 * Zettacast\Filesystem\Driver\LocalDriver class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Filesystem\Driver;

use Zettacast\Filesystem\File;
use Zettacast\Filesystem\Info;
use Zettacast\Collection\Sequence;
use Zettacast\Contract\Stream\StreamInterface;
use Zettacast\Contract\Filesystem\DriverInterface;
use Zettacast\Contract\Collection\SequenceInterface;
use Zettacast\Exception\Filesystem\FilesystemException;

/**
 * Driver for local files. This driver handles all operations to the local
 * filesystem, where the framework is installed. It cannot handle files out of
 * the document root, though.
 * @package Zettacast\Filesystem
 * @version 1.0
 */
class LocalDriver implements DriverInterface
{
	/**
	 * Driver root. All operations will use this directory as base, that is
	 * it is used as a prefix for all paths used in the object.
	 * @var string Prefix to all relative paths in this object.
	 */
	protected $prefix;
	
	/**
	 * Local driver constructor.
	 * @param string $root Root directory for all operations done in driver.
	 * @throws FilesystemException The path does not exist or cannot be read.
	 */
	public function __construct(string $root = DOCROOT)
	{
		$root = realpath($root);
		$this->prefix = rtrim($root, '\\/');
		
		if(!$this->ensure($root))
			throw FilesystemException::missingDirectory($root);
	}
	
	/**
	 * Checks whether a path exists in the driver.
	 * @param string $path Path to be checked.
	 * @return bool Was the path found?
	 */
	public function has(string $path): bool
	{
		$src = $this->prefix($path);
		return file_exists($src);
	}
	
	/**
	 * Removes a file from driver.
	 * @param string $path Path to file to be removed from driver.
	 * @return bool Was file or directory successfully removed?
	 */
	public function remove(string $path): bool
	{
		$tgt = $this->prefix($path);
		return @unlink($tgt);
	}
	
	/**
	 * Edits the permission information of the given path.
	 * @param string $path Path to be editted.
	 * @param int $perms Permission to be set to given path.
	 * @return bool Was permission successfully executed?
	 */
	public function chmod(string $path, int $perms = 0777): bool
	{
		return $this->has($path)
			? chmod($this->prefix($path), $perms)
			: false;
	}
	
	/**
	 * Creates a copy of a file in the given destiny path.
	 * @param string $path File to be copied.
	 * @param string $target Path to which copy is created.
	 * @return bool Was it possible to copy such a file?
	 */
	public function copy(string $path, string $target): bool
	{
		if(!$this->has($path))
			return false;
		
		$src = $this->prefix($path);
		$tgt = $this->prefix($target);
		return $this->ensure(dirname($tgt)) && copy($src, $tgt);
	}
	
	/**
	 * Returns all metadata available for given path.
	 * @param string $path Target path for metadata request.
	 * @param string $data Specific data to be retrieved.
	 * @return mixed All metadata values or retrieved specific data.
	 */
	public function info(string $path = null, string $data = null)
	{
		$src = $this->prefix($path ?? '.');
		$data = !is_null($data) ? 'get'.$data : null;
		
		return !is_null($data)
			? method_exists(Info::class, $data)
				? with(new Info($src))->$data()
				: null
			: new Info($src);
	}
	
	/**
	 * Checks whether the given path is a directory.
	 * @param string $path Path to be checked.
	 * @return bool Is path a directory?
	 */
	public function isDir(string $path): bool
	{
		$src = $this->prefix($path);
		return is_dir($src);
	}
	
	/**
	 * Checks whether the given path is a file.
	 * @param string $path Path to be checked.
	 * @return bool Is path a file?
	 */
	public function isFile(string $path): bool
	{
		$src = $this->prefix($path);
		return is_file($src);
	}
	
	/**
	 * Lists all files and directories contained in the given path.
	 * @param string $dir Path to be listed.
	 * @return SequenceInterface All directory contents in the path.
	 */
	public function list(string $dir = null): SequenceInterface
	{
		if(!is_dir($src = $this->prefix($dir ?? '')))
			return new Sequence;
		
		return with(new Sequence(scandir($src)))->slice(2);
	}
	
	/**
	 * Creates a new directory into the driver.
	 * @param string $path Path of the directory to be created.
	 * @param int $perms Permission to be given to the new directory.
	 * @return bool Was the directory successfully created?
	 */
	public function mkdir(string $path, int $perms = 0777): bool
	{
		$tgt = $this->prefix($path);
		return !is_dir($tgt) && mkdir($tgt, $perms, true);
	}
	
	/**
	 * Moves given file or directory to another location.
	 * @param string $path Target path, that will be moved.
	 * @param string $newpath The new name for target file or directory.
	 * @return bool Was the file or directory successfully moved?
	 */
	public function move(string $path, string $newpath): bool
	{
		if(!$this->has($path))
			return false;

		$src = $this->prefix($path);
		$tgt = $this->prefix($newpath);
		return $this->ensure(dirname($tgt)) && rename($src, $tgt);
	}
	
	/**
	 * Opens a file as a directly editable stream.
	 * @param string $filename File to be opened.
	 * @param string $mode Reading/writing mode the file should be opened in.
	 * @return StreamInterface The directly editable file handler.
	 */
	public function open(string $filename, string $mode = 'r'): StreamInterface
	{
		$tgt = $this->prefix($filename);
		return is_file($tgt) ? new File($tgt, $mode) : null;
	}
	
	/**
	 * Retrieves all contents from the given file.
	 * @param string $filename File to be read.
	 * @return string All file contents.
	 */
	public function read(string $filename): string
	{
		if(!$this->has($filename))
			return (string)null;
		
		$src = $this->prefix($filename);
		return file_get_contents($src);
	}
	
	/**
	 * Retrieves contents from a file and puts it into a stream.
	 * @param string $file Source file to be read.
	 * @param resource|StreamInterface $stream Target stream to put content on.
	 * @param int $length Maximum number of bytes to be written to stream.
	 * @return int Length of data read from file.
	 */
	public function readTo(string $file, $stream, int $length = null): int
	{
		$fcontent = $this->read($file);

		return $stream instanceof StreamInterface
			? $stream->write($fcontent, $length)
			: fwrite($stream, $fcontent, $length ?? strlen($fcontent));
	}
	
	/**
	 * Removes a directory from driver.
	 * @param string $path Path to directory to be removed from driver.
	 * @return bool Was directory successfully removed?
	 */
	public function rmdir(string $path): bool
	{
		if(!$this->isdir($path))
			return false;

		$tgt = $this->prefix($path);
		$list = $this->list($path);
		
		foreach($list as $file)
			$this->isdir("$path/$file")
				? $this->rmdir("$path/$file")
				: $this->remove("$path/$file");
			
		return @rmdir($tgt);
	}
	
	/**
	 * Appends the content to a file, that will be created if needed.
	 * @param string $filename Target file path to be written.
	 * @param mixed $content Content to be written to path.
	 * @return int Number of written characters.
	 */
	public function update(string $filename, $content): int
	{
		$tgt = $this->prefix($filename);
		
		return $this->ensure(dirname($tgt))
			? file_put_contents($tgt, $content, FILE_APPEND)
			: 0;
	}
	
	/**
	 * Retrieves content from stream and appends it to a file.
	 * @param resource|StreamInterface $stream Source content is retrieved from.
	 * @param string $file Target file to be written to.
	 * @param int $length Maximum number of bytes to be written to file.
	 * @return int Total length of data written to file.
	 */
	public function updateFrom($stream, string $file, int $length = null): int
	{
		return $stream instanceof StreamInterface
			? $this->update($file, $stream->read($length))
			: $this->update($file, stream_get_contents($stream, $length));
	}
	
	/**
	 * Writes the content to a file, that will be created if needed.
	 * @param string $filename Target file path to be written.
	 * @param mixed $content Content to be written to path.
	 * @return int Number of written characters.
	 */
	public function write(string $filename, $content): int
	{
		$tgt = $this->prefix($filename);
		
		return $this->ensure(dirname($tgt))
			? file_put_contents($tgt, $content)
			: 0;
	}
	
	/**
	 * Retrieves content from stream and writes it to a file.
	 * @param resource|StreamInterface $stream Stream content is retrieved from.
	 * @param string $file Target file to be written to.
	 * @param int $length Maximum number of bytes to be written to file.
	 * @return int Total length of data written to file.
	 */
	public function writeFrom($stream, string $file, int $length = null): int
	{
		return $stream instanceof StreamInterface
			? $this->write($file, $stream->read($length))
			: $this->write($file, stream_get_contents($stream, $length));
	}
	
	/**
	 * Checks whether a directory exists and creates it if needed.
	 * @param string $path Path to be ensured it exists.
	 * @return bool Does directory exist or was successfully created?
	 */
	protected function ensure(string $path): bool
	{
		if(!is_dir($path)) {
			$umask = umask(0);
			@mkdir($path, 0755);
			umask($umask);
		}
		
		return is_dir($path) && is_readable($path);
	}
	
	/**
	 * Applies the base prefix to given path.
	 * @param string $path Path to be prefixed.
	 * @return string Prefixed path.
	 */
	protected function prefix(string $path): string
	{
		return $this->prefix.'/'.ltrim($path, '\\/');
	}
	
	/**
	 * Removes the base prefix from given string.
	 * @param string $path Path to be unprefixed.
	 * @return string Unprefixed path.
	 */
	protected function unprefix(string $path): string
	{
		return substr($path, strlen($this->prefix.'/'));
	}
	
}
