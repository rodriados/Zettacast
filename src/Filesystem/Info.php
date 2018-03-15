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
 * @property string $basename The base name of the file.
 * @property string $dirname The path without filename.
 * @property string $extension The file extension.
 * @property string $path The path to the file.
 * @property int $perms The file permissions.
 * @property int $permissions The file permissions.
 * @property string $realpath The absolute path to file.
 * @property int $size The file size.
 * @property int $timestamp The last modified time.
 * @property int $atime The last access time of the file.
 * @property int $ctime The file last change time.
 * @property int $mtime The last modified time.
 * @property string $type The file type.
 * @package Zettacast\Filesystem
 * @version 1.0
 */
class Info
{
	/**
	 * Base object for information about a file or directory.
	 * @var \SplFileInfo Information object.
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
	 * Info access property magic method.
	 * Allows access to file or directory info.
	 * @param string $property The requested property.
	 * @return mixed The property's value.
	 */
	public function __get(string $property)
	{
		static $info = [
			'basename'      => 'getBasename',
			'dirname'       => 'getPath',
			'extension'     => 'getExtension',
			'path'          => 'getPathname',
			'perms'         => 'getPerms',
			'permissions'   => 'getPerms',
			'realpath'      => 'getRealPath',
			'size'          => 'getSize',
			'timestamp'     => 'getMTime',
			'atime'         => 'getATime',
			'ctime'         => 'getCTime',
			'mtime'         => 'getMTime',
			'type'          => 'getType',
		];
		
		return isset($info[$property])
			? $this->spl->{$info[$property]}()
			: null;
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
	public function open(string $mode = 'r'): File
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
	public function parent(): Info
	{
		$clone = clone $this;
		$clone->spl = $this->spl->getPathInfo();
		return $clone;
	}
}
