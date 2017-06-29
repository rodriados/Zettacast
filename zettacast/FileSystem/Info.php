<?php
/**
 * Zettacast\FileSystem\Info class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\FileSystem;

use Exception;
use SplFileInfo;
use Zettacast\Collection\Contract\Collection;

class Info implements Collection
{
	public $spl;
	
	protected static $custom = [];
	
	protected static $funcnames = [
		'mime'          => [self::class, 'mime'],
		'parent'        => [self::class, 'parent'],
		'isdir'         => [SplFileInfo::class, 'isDir'],
		'isfile'        => [SplFileInfo::class, 'isFile'],
		'islink'        => [SplFileInfo::class, 'isLink'],
		'path'          => [SplFileInfo::class, 'getPath'],
		'size'          => [SplFileInfo::class, 'getSize'],
		'type'          => [SplFileInfo::class, 'getType'],
		'group'         => [SplFileInfo::class, 'getGroup'],
		'inode'         => [SplFileInfo::class, 'getInode'],
		'owner'         => [SplFileInfo::class, 'getOwner'],
        'basename'      => [SplFileInfo::class, 'getBasename'],
        'extension'     => [SplFileInfo::class, 'getExtension'],
        'filename'      => [SplFileInfo::class, 'getFilename'],
        'timestamp'     => [SplFileInfo::class, 'getMTime'],
        'pathname'      => [SplFileInfo::class, 'getPathname'],
        'permission'    => [SplFileInfo::class, 'getPerms'],
        'realpath'      => [SplFileInfo::class, 'getRealPath'],
        'executable'    => [SplFileInfo::class, 'isExecutable'],
        'readable'      => [SplFileInfo::class, 'isReadable'],
		'writable'      => [SplFileInfo::class, 'isWritable'],
	];
	
	public function __construct($filename = DOCROOT)
	{
		if($filename instanceof SplFileInfo)
			$this->spl = $filename;
		
		elseif($filename instanceof self)
			$this->spl = $filename->spl;
		
		elseif(is_string($filename))
			$this->spl = new SplFileInfo($filename);
		
		else throw new Exception('Unable to gather info about given path');
	}

	public function __toString() : string
	{
		return $this->spl->__toString();
	}
	
	/**
	 * Returns all data stored in collection.
	 * @return array All data stored in collection.
	 */
	public function all()
	{
		foreach(array_keys(self::$funcnames) as $key)
			$data[$key] = $this->get($key);
		
		return array_merge(
			$data ?? [],
			self::$custom[$this->spl->getRealPath()] ?? []
		);
	}
	
	/**
	 * Counts the number of elements currently in collection.
	 * @return int Number of elements stored in the collection.
	 */
	public function count()
	{
		return count($this->all());
	}
	
	/**
	 * Removes an element from collection.
	 * @param mixed $key Key to be removed.
	 */
	public function del($key)
	{
		unset(self::$custom[$this->spl->getRealPath()][$key]);
	}
	
	/**
	 * Checks whether collection is currently empty.
	 * @return bool Is collection empty?
	 */
	public function empty()
	{
		return false;
	}
	
	/**
	 * Get an element stored in collection.
	 * @param mixed $key Key of requested element.
	 * @param mixed $default Default value fallback.
	 * @return mixed Requested element or default fallback.
	 */
	public function get($key, $default = null)
	{
		if(!isset(self::$funcnames[$key]))
			return self::$custom[$this->spl->getRealPath()][$key] ?? $default;
		
		list($class, $method) = self::$funcnames[$key];
		
		$instances = [
			SplFileInfo::class  => $this->spl,
			self::class         => $this,
		];
		
		return $instances[$class]->$method();
	}
	
	/**
	 * Checks whether element key exists.
	 * @param mixed $key Key to be check if exists.
	 * @return bool Does key exist?
	 */
	public function has($key)
	{
		return isset(self::$funcnames[$key])
			or isset(self::$custom[$this->spl->getRealPath()][$key]);
	}
	
	public function parent() : self
	{
		if($this->spl->getRealPath() == DOCROOT)
			return $this;
		
		return new static($this->spl->getPathInfo());
	}
	
	public function open(string $mode = 'r') : File
	{
		return $this->spl->isFile()
			? new File($this->spl->getRealPath(), $mode)
			: null;
	}
	
	/**
	 * Sets a value to the given key.
	 * @param mixed $key Key to created or updated.
	 * @param mixed $value Value to be stored in key.
	 */
	public function set($key, $value)
	{
		self::$custom[$this->spl->getRealPath()][$key] = $value;
	}
	
	public function mime()
	{
		return ($type = $this->spl->getType()) === 'file'
			? with(new \finfo(FILEINFO_MIME_TYPE))
				->file($this->spl->getRealPath())
			: $type;
	}
	
}
