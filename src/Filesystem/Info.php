<?php
/**
 * Zettacast\Filesystem\Info class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Filesystem;

use SplFileInfo;

/**
 * Info class gathers information about local files and directories. This is
 * almost a simple wrapper around SplFileInfo.
 * @package Zettacast\Filesystem
 */
class Info
{
	/**
	 * Base object for information about a file or directory.
	 * @var SplFileInfo Information object.
	 */
	protected $spl;
	
	/**
	 * Info constructor. Builds the internal file information object.
	 * @param string $filename File or directory to be inspected.
	 */
	public function __construct(string $filename = DOCROOT)
	{
		$this->spl = new SplFileInfo($filename);
	}
	
	/**
	 * Expresses this object as a string, so it can be easily used as one.
	 * @return string The spicified path during object construction.
	 */
	public function __toString() : string
	{
		return $this->spl->__toString();
	}
	
	/**
	 * This method returns the base name of the file, directory, or link
	 * without path info.
	 * @return string The base name of the file.
	 */
	public function basename() : string
	{
		return $this->spl->getBasename();
	}
	
	/**
	 * Gets the path without filename.
	 * @return string Dirname of targeted file.
	 */
	public function dirname() : string
	{
		return $this->spl->getPath();
	}
	
	/**
	 * Checks whether the file is executable.
	 * @return bool Is the file executable?
	 */
	public function executable() : bool
	{
		return $this->spl->isExecutable();
	}
	
	/**
	 * Retrieves the file extension.
	 * @return string The file extension.
	 */
	public function extension() : string
	{
		return $this->spl->getExtension();
	}
	
	/**
	 * Checks whether path is directory.
	 * @return bool Is path a directory?
	 */
	public function isdir() : bool
	{
		return $this->spl->isDir();
	}
	
	/**
	 * Checks whether path is file.
	 * @return bool Is path a file?
	 */
	public function isfile() : bool
	{
		return $this->spl->isFile();
	}
	
	/**
	 * Checks whether path is link.
	 * @return bool Is path a link?
	 */
	public function islink() : bool
	{
		return $this->spl->isLink();
	}
	
	/**
	 * Guesses the targeted path mimetype. If directory or link, the guess is
	 * trivial. For files, the mimetype is guessed based on file's contents.
	 * @return string Path guessed mimetype.
	 */
	public function mime() : string
	{
		return ($type = $this->spl->getType()) === 'file'
			? with(new \finfo(FILEINFO_MIME_TYPE))
				->file($this->spl->getRealPath())
			: $type;
	}
	
	/**
	 * If the currently targeted path is a file, opens it as a stream and
	 * nothing is done otherwise.
	 * @param string $mode File opening mode.
	 * @return File Stream to file contents.
	 */
	public function open(string $mode = 'r') : File
	{
		return $this->spl->isFile()
			? new File($this->spl->getRealPath(), $mode)
			: null;
	}
	
	/**
	 * Accesses information about the directory that is parent of the currently
	 * targeted file or directory.
	 * @return static Parent directory of this target.
	 */
	public function parent()
	{
		$clone = clone $this;
		$clone->spl = $this->spl->getPathInfo();
		return $clone;
	}
	
	/**
	 * Returns the path to the file.
	 * @return string The path to the file.
	 */
	public function path() : string
	{
		return $this->spl->getPathname();
	}
	
	/**
	 * Gets the file permissions for the file.
	 * @return int The file permissions.
	 */
	public function permissions() : int
	{
		return $this->spl->getPerms();
	}
	
	/**
	 * Checks whether the file is readable.
	 * @return bool Is the file readable?
	 */
	public function readable() : bool
	{
		return $this->spl->isReadable();
	}
	
	/**
	 * This method expands all symbolic links, resolves relative references and
	 * returns the real path to the file.
	 * @return string The absolute path to file.
	 */
	public function realpath() : string
	{
		return $this->spl->getRealPath();
	}
	
	/**
	 * Returns the filesize in bytes for the file referenced.
	 * @return int The file size.
	 */
	public function size() : int
	{
		return $this->spl->getSize();
	}
	
	/**
	 * Returns the time when the contents of the file were changed. The time
	 * returned is a Unix timestamp.
	 * @return int The last modified time.
	 */
	public function timestamp() : int
	{
		return $this->spl->getMTime();
	}
	
	/**
	 * Returns the type of the file referenced.
	 * @return string The file type.
	 */
	public function type() : string
	{
		return $this->spl->getType();
	}
	
	/**
	 * Checks whether the file is writable.
	 * @return bool Is the file writable?
	 */
	public function writable() : bool
	{
		return $this->spl->isWritable();
	}
	
}
