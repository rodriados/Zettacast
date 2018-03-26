<?php
/**
 * Zettacast\Filesystem\Info class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Filesystem;

/**
 * The file information class gathers information about local files and
 * directories. This is almost a simple wrapper around SplFileInfo.
 * @package Zettacast\Filesystem
 * @version 1.0
 */
class Info
{
	/**
	 * Base object for information about a file or directory.
	 * @var \SplFileInfo Built-in information object.
	 */
	protected $spl;
	
	/**
	 * Info constructor.
	 * Builds the internal file information object.
	 * @param string $filename File or directory to inspect.
	 */
	public function __construct(string $filename = ROOTPATH)
	{
		$this->spl = new \SplFileInfo($filename);
	}
	
	/**
	 * Info string representation magic method.
	 * Expresses this object as a string, so it can be easily used as one.
	 * @return string The spicified path during object construction.
	 */
	public function __tostring(): string
	{
		return (string)$this->spl;
	}
	
	/**
	 * Checks whether path is directory.
	 * @return bool Is path a directory?
	 */
	public function isdir(): bool
	{
		return $this->spl->isDir();
	}
	
	/**
	 * Checks whether path is file.
	 * @return bool Is path a file?
	 */
	public function isfile(): bool
	{
		return $this->spl->isFile();
	}
	
	/**
	 * Checks whether path is link.
	 * @return bool Is path a link?
	 */
	public function islink(): bool
	{
		return $this->spl->isLink();
	}
	
	/**
	 * Checks whether path is executable.
	 * @return bool Is path executable?
	 */
	public function executable(): bool
	{
		return $this->spl->isExecutable();
	}
	
	/**
	 * Checks whether path is readable.
	 * @return bool Is path readable?
	 */
	public function readable(): bool
	{
		return $this->spl->isReadable();
	}
	
	/**
	 * Checks whether path is writable.
	 * @return bool Is path writable?
	 */
	public function writable(): bool
	{
		return $this->spl->isWritable();
	}
	
	/**
	 * Guesses targeted path mimetype. If directory or link, the guess is
	 * trivial. For files, the mimetype is guessed based on file's contents.
	 * @return string Path guessed mimetype.
	 */
	public function mime(): string
	{
		return ($type = $this->type()) === 'file'
			? with(new \finfo(FILEINFO_MIME_TYPE))->file($this->realpath())
			: $type;
	}
	
	/**
	 * If the currently targeted path is a file, opens it as a stream and
	 * nothing is done otherwise.
	 * @param string $mode File opening mode.
	 * @return File Stream to file contents.
	 */
	public function open(string $mode = 'r'): ?File
	{
		return $this->isfile()
			? new File($this->realpath(), $mode)
			: null;
	}
	
	/**
	 * Accesses information about the directory that is parent of the currently
	 * targeted file or directory.
	 * @return static Parent directory of this target.
	 */
	public function parent(): Info
	{
		$clone = clone $this;
		$clone->spl = $this->spl->getPathInfo();
		return $clone;
	}
	
	/**
	 * Retrieves the base name of path.
	 * @return string The path's base name.
	 */
	public function basename(): string
	{
		return $this->spl->getBasename();
	}
	
	/**
	 * Retrieves the directory name of path.
	 * @return string The path's directory name.
	 */
	public function dirname(): string
	{
		return $this->spl->getPath();
	}
	
	/**
	 * Retrieves the extension of file.
	 * @return string The file's extension.
	 */
	public function extension(): ?string
	{
		return $this->spl->getExtension() ?: null;
	}
	
	/**
	 * Retrieves the path to file or directory.
	 * @return string The file or directory's path.
	 */
	public function path(): string
	{
		return $this->spl->getPathname();
	}
	
	/**
	 * Retrieves the permissions of path.
	 * @return int The path's permissions.
	 */
	public function perms(): int
	{
		return $this->spl->getPerms();
	}
	
	/**
	 * Retrieves the absolute path of file or directory.
	 * @return string The file or directory's absolute path.
	 */
	public function realpath(): ?string
	{
		return $this->spl->getRealPath() ?: null;
	}
	
	/**
	 * Retrieves the size of file.
	 * @return int The file's size.
	 */
	public function size(): int
	{
		return $this->spl->getSize();
	}
	
	/**
	 * Retrieves the path last access time.
	 * @return int The last access time of path.
	 */
	public function atime(): int
	{
		return $this->spl->getATime();
	}
	
	/**
	 * Retrieves the path creation time. It might not be accurate depending on
	 * the system. In most UNIX based systems there is no creation time.
	 * @return int The creation time of path.
	 */
	public function ctime(): int
	{
		return $this->spl->getCTime();
	}
	
	/**
	 * Retrieves the path last modified time.
	 * @return int The last modified time of path.
	 */
	public function mtime(): int
	{
		return $this->spl->getMTime();
	}
	
	/**
	 * Retrieves the path last modified time.
	 * @return int The last modified time of path.
	 */
	public function timestamp(): int
	{
		return $this->mtime();
	}
	
	/**
	 * Informs the type of path.
	 * @return string The type of path.
	 */
	public function type(): string
	{
		return $this->spl->getType();
	}
}
