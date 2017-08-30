<?php
/**
 * Zettacast\Filesystem\Driver\Local class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Filesystem\Driver;

use Zettacast\Filesystem\File;
use Zettacast\Filesystem\Info;
use Zettacast\Collection\Sequence;
use Zettacast\Filesystem\Exception\MissingDirectory;
use Zettacast\Contract\Filesystem\Stream as StreamContract;
use Zettacast\Contract\Filesystem\Driver as DriverContract;

/**
 * Driver for local files. This driver handles all operations to the local
 * filesystem, where the framework is installed. It cannot handle files out of
 * the document root, though
 * @method string basename(string $path = null)
 * @method string dirname(string $path = null)
 * @method bool executable(string $path = null)
 * @method string extension(string $path = null)
 * @method bool islink(string $path = null)
 * @method string mime(string $path = null)
 * @method Info parent(string $path = null)
 * @method string path(string $path = null)
 * @method int permissions(string $path = null)
 * @method bool readable(string $path = null)
 * @method string realpath(string $path = null)
 * @method int size(string $path = null)
 * @method int timestamp(string $path = null)
 * @method string type(string $path = null)
 * @method bool writable(string $path = null)
 * @package Zettacast\Filesystem
 * @version 1.0
 */
class Local
	implements DriverContract
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
	 * @throws MissingDirectory The path does not exist or can not be read.
	 */
	public function __construct(string $root = DOCROOT)
	{
		$root = realpath($root);
		$this->prefix = rtrim($root, '\\/');
		
		if(!$this->ensure($root))
			throw new MissingDirectory($root);
	}
	
	/**
	 * Retrieves metadata about a file or directory from driver.
	 * @param string $name Name of metadata being retrieved.
	 * @param array $args File path to be informed about.
	 * @return mixed Retrieved metadata about path, or default return value.
	 */
	public function __call(string $name, array $args)
	{
		return $this->info($args[0] ?? '', $name);
	}
	
	/**
	 * Creates a copy of a file in the given destiny path.
	 * @param string $path File to be copied.
	 * @param string $target Path to which copy is created.
	 * @return bool Was it possible to copy such a file?
	 */
	public function copy(string $path, string $target) : bool
	{
		if(!$this->has($path))
			return false;
		
		$src = $this->prefix($path);
		$tgt = $this->prefix($target);
		return $this->ensure(dirname($tgt)) && copy($src, $tgt);
	}
	
	/**
	 * Checks whether a path exists in the driver.
	 * @param string $path Path to be checked.
	 * @return bool Was the path found?
	 */
	public function has(string $path) : bool
	{
		$src = $this->prefix($path);
		return file_exists($src);
	}
	
	/**
	 * Returns all metadata available for given path.
	 * @param string $path Target path for metadata request.
	 * @param string $data Specific data to be retrieved.
	 * @return mixed All metadata values or retrieved specific data.
	 */
	public function info(string $path = null, string $data = null)
	{
		$src = $this->prefix($path ?? '');
		
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
	public function isdir(string $path) : bool
	{
		$src = $this->prefix($path);
		return is_dir($src);
	}
	
	/**
	 * Checks whether the given path is a file.
	 * @param string $path Path to be checked.
	 * @return bool Is path a file?
	 */
	public function isfile(string $path) : bool
	{
		$src = $this->prefix($path);
		return is_file($src);
	}
	
	/**
	 * Lists all files and directories contained in the given path.
	 * @param string $dir Path to be listed.
	 * @return Sequence All directory contents in the path.
	 */
	public function list(string $dir = null) : Sequence
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
	public function mkdir(string $path, int $perms = 0777) : bool
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
	public function move(string $path, string $newpath) : bool
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
	 * @return StreamContract The directly editable file handler.
	 */
	public function open(string $filename, string $mode = 'r') : StreamContract
	{
		$tgt = $this->prefix($filename);
		return is_file($tgt) ? new File($tgt, $mode) : null;
	}
	
	/**
	 * Edits the permission information of the given path.
	 * @param string $path Path to be editted.
	 * @param int $perms Permission to be set to given path.
	 * @return bool Was permission successfully executed?
	 */
	public function permission(string $path, int $perms = 0777) : bool
	{
		if(!$this->has($path))
			return false;
		
		$tgt = $this->prefix($path);
		return chmod($tgt, $perms);
	}
	
	/**
	 * Retrieves all contents from the given file.
	 * @param string $filename File to be read.
	 * @return string All file contents.
	 */
	public function read(string $filename) : string
	{
		if(!$this->has($filename))
			return (string)null;
		
		$src = $this->prefix($filename);
		return file_get_contents($src);
	}
	
	/**
	 * Removes a file from driver.
	 * @param string $path Path to file to be removed from driver.
	 * @return bool Was file or directory successfully removed?
	 */
	public function remove(string $path) : bool
	{
		$tgt = $this->prefix($path);
		return @unlink($tgt);
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
	public function update(string $filename, $content) : int
	{
		$tgt = $this->prefix($filename);
		
		return $this->ensure(dirname($tgt))
			? file_put_contents($tgt, $content, FILE_APPEND)
			: 0;
	}
	
	/**
	 * Writes the content to a file, that will be created if needed.
	 * @param string $filename Target file path to be written.
	 * @param mixed $content Content to be written to path.
	 * @return int Number of written characters.
	 */
	public function write(string $filename, $content) : int
	{
		$tgt = $this->prefix($filename);
		
		return $this->ensure(dirname($tgt))
			? file_put_contents($tgt, $content)
			: 0;
	}
	
	/**
	 * Checks whether a directory exists and creates it if needed.
	 * @param string $path Path to be ensured it exists.
	 * @return bool Does directory exist or was successfully created?
	 */
	protected function ensure(string $path)
	{
		if(!is_dir($path)) {
			$umask = umask(0);
			@mkdir($path, 0755, true);
			umask($umask);
		}
		
		return is_dir($path) && is_readable($path);
	}
	
	/**
	 * Applies the base prefix to given path.
	 * @param string $path Path to be prefixed.
	 * @return string Prefixed path.
	 */
	protected function prefix(string $path)
	{
		return $this->prefix.'/'.ltrim($path, '\\/');
	}
	
	/**
	 * Removes the base prefix from given string.
	 * @param string $path Path to be unprefixed.
	 * @return string Unprefixed path.
	 */
	protected function unprefix(string $path)
	{
		return substr($path, strlen($this->prefix.'/'));
	}
	
}
