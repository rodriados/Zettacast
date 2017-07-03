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
use Zettacast\Collection\Contract\Collection;

/**
 * Driver for local files. This driver handles all operations to the local
 * filesystem, where the framework is installed. It cannot handle files out of
 * the document root, though.
 * @package Zettacast\FileSystem\Driver
 * @version 1.0
 */
class Local implements Driver
{
	/**
	 * Driver root. All operations will happen inside this directory, that is
	 * used as a prefix for all paths used in the object.
	 * @var string Prefix to all relative paths in this object.
	 */
	protected $prefix;
	
	/**
	 * Local driver constructor.
	 * @param string $root Root directory for all operations done in driver.
	 * @throws Exception The root path can not be read.
	 */
	public function __construct(string $root = DOCROOT)
	{
		$root = is_link($root) ? realpath($root) : $root;
		$this->ensure($root);
		
		if(!is_dir($root) or !is_readable($root))
			throw new Exception('The root path '.$root.' is not readable.');
		
		$this->prefix = $root ? rtrim($root, '\\/').'/' : null;
	}
	
	/**
	 * Retrieves metadata from driver.
	 * @param string $name Name of metadata being retrieved.
	 * @param array $arguments File path and default value if not found.
	 * @return Collection
	 */
	public function __call(string $name, array $arguments)
	{
		$location = $this->prefix($arguments[0]);
		return $this->meta($location, $name, $arguments[1] ?? null);
	}
	
	/**
	 * Creates a copy of a file in the given destiny path.
	 * @param string $path File to be copied.
	 * @param string $target Path to which copy is created.
	 * @return bool Was it possible to copy such a file?
	 */
	public function copy(string $path, string $target) : bool
	{
		$location = $this->prefix($path);
		$destiny = $this->prefix($target);
		$this->ensure($this->prefix(dirname($destiny)));
		
		return copy($location, $destiny);
	}
	
	/**
	 * Checks whether a path exists in the driver.
	 * @param string $path Path to be checked.
	 * @return bool Was the path found?
	 */
	public function has(string $path) : bool
	{
		$location = $this->prefix($path);
		return file_exists($location);
	}
	
	/**
	 * Checks whether the given path is a directory.
	 * @param string $path Path to be checked.
	 * @return bool Is path a directory?
	 */
	public function isdir(string $path) : bool
	{
		$location = $this->prefix($path);
		return is_dir($location);
	}
	
	/**
	 * Checks whether the given path is a file.
	 * @param string $path Path to be checked.
	 * @return bool Is path a file?
	 */
	public function isfile(string $path) : bool
	{
		$location = $this->prefix($path);
		return is_file($location);
	}
	
	/**
	 * Lists all files and directories contained in the given path.
	 * @param string $dir Path to be listed.
	 * @return array All directory contents in the path.
	 */
	public function list(string $dir = null) : array
	{
		if(!is_dir($location = $this->prefix($dir)))
			return [];
		
		return array_slice(scandir($location), 2);
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
		$location = $this->prefix($path);
		$metadata = new Info($location);
		return $data ? $metadata->get($data, $default) : $metadata;
	}
	
	/**
	 * Creates a new directory into the driver.
	 * @param string $path Path of the directory to be created.
	 * @param int $perms Permission to be given to the new directory.
	 * @return bool Was the directory successfully created?
	 */
	public function mkdir(string $path, int $perms = 0777) : bool
	{
		$location = $this->prefix($path);
		return (!is_dir($location) && mkdir($location, $perms, true));
	}
	
	/**
	 * Opens a file as a directly editable object.
	 * @param string $filename File to be opened.
	 * @param string $mode Reading/writing mode the file should be opened in.
	 * @return Handler The directly editable file handler.
	 */
	public function open(string $filename, string $mode = 'r') : Handler
	{
		$location = $this->prefix($filename);
		return new File($location, $mode);
	}
	
	/**
	 * Edits the permission information of the given path.
	 * @param string $path Path to be editted.
	 * @param int $perms Permission to be set to given path.
	 * @return bool Was permission successfully executed?
	 */
	public function permission(string $path, int $perms = 0777) : bool
	{
		$location = $this->prefix($path);
		return chmod($location, $perms);
	}
	
	/**
	 * Retrieves all contents from the given file.
	 * @param string $filename File to be read.
	 * @return string All file contents.
	 */
	public function read(string $filename) : string
	{
		$location = $this->prefix($filename);
		return file_get_contents($location);
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
		$location = $this->prefix($path);
		return unlink($location);
	}
	
	/**
	 * Renames given file or directory and moves it, if needed.
	 * @param string $path Target path, that will be renamed.
	 * @param string $newpath The new name for target file or directory.
	 * @return bool Was the file or directory successfully renamed?
	 */
	public function rename(string $path, string $newpath) : bool
	{
		$location = $this->prefix($path);
		$destiny = $this->prefix($newpath);
		$this->ensure($this->prefix(dirname($destiny)));
		
		return rename($location, $destiny);
	}
	
	/**
	 * Removes a directory from driver.
	 * @param string $path Path to directory to be removed from driver.
	 * @return bool Was directory successfully removed?
	 */
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
	
	/**
	 * Writes the content to a file, that will be created if needed.
	 * @param string $filename Target file path to be written.
	 * @param mixed $content Content to be written to path.
	 * @return int Number of written characters.
	 */
	public function write(string $filename, $content, int $flags = null) : int
	{
		$location = $this->prefix($filename);
		$this->ensure($this->prefix(dirname($location)));
		
		return file_put_contents($location, $content, $flags);
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
		$location = $this->prefix($filename);
		$this->ensure($this->prefix(dirname($location)));
		
		return with(new File($location, 'w+b'))->writeFrom($source);
	}
	
	/**
	 * Checks whether a directory exists and creates it, if not.
	 * @param string $path Path to be ensured it exists.
	 * @throws Exception Impossible to create directory.
	 */
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
	
	/**
	 * Prefixes all paths given to object.
	 * @param string $path Path to be prefixed.
	 * @return string Prefixed path.
	 */
	protected function prefix(string $path)
	{
		return $this->prefix.ltrim($path, '\\/');
	}
	
	/**
	 * Unprefixes a path.
	 * @param string $path Path to be unprefixed.
	 * @return string Unprefixed path.
	 */
	protected function unprefix(string $path)
	{
		return substr($path, strlen($this->prefix));
	}
	
}
