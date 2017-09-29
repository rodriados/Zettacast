<?php
/**
 * Zettacast\Filesystem\Disk\ZipDisk class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Filesystem\Disk;

use Zettacast\Stream\Stream;
use Zettacast\Collection\Sequence;
use Zettacast\Collection\Collection;
use Zettacast\Contract\Stream\StreamInterface;
use Zettacast\Contract\Filesystem\DiskInterface;
use Zettacast\Contract\Collection\SequenceInterface;
use Zettacast\Exception\Filesystem\FilesystemException;

/**
 * Disk for accessing a zip file. This driver allows the stored zip files to be
 * treated as if they were local uncompressed files.
 * @package Zettacast\Filesystem
 * @version 1.0
 */
class ZipDisk implements DiskInterface
{
	/**
	 * The internal zip file handler resource.
	 * @var \ZipArchive The object responsible for actually accessing the file.
	 */
	protected $archive;
	
	protected $password;
	
	/**
	 * This constructor is responsible for opening or creating the zip file.
	 * @param string $location The location of the zip file to be opened.
	 * @param string $password Password needed to decompress the file.
	 * @throws FilesystemException The zip file could not be found or created.
	 */
	public function __construct(string $location, string $password = null)
	{
		$this->archive = new \ZipArchive;
		$success = $this->archive->open($location, \ZipArchive::CREATE);
		
		if(!is_null($this->password = $password))
			$this->archive->setPassword($this->password);
		
		if($success !== true)
			throw FilesystemException::missingFile($location);
	}
	
	/**
	 * Closes the zip file and commits all changes made to it. This constructor
	 * is explicitly needed as the zip archive will come back to its original
	 * form if not previously closed.
	 */
	public function __destruct()
	{
		$this->archive->close();
	}
	
	/**
	 * Checks whether a path exists in the disk.
	 * @param string $path Path to be checked.
	 * @return bool Was the path found?
	 */
	public function has(string $path): bool
	{
		$tgt = $this->treat($path);
		
		return (bool)$this->archive->statName($tgt)
		       or (bool)$this->archive->statName($tgt.'/');
	}
	
	/**
	 * Removes a file from disk.
	 * @param string $path Path to file to be removed from disk.
	 * @return bool Was file or directory successfully removed?
	 */
	public function remove(string $path): bool
	{
		return $this->archive->deleteName($path);
	}
	
	/**
	 * Creates a copy of a file in the given destiny path.
	 * @param string $path File to be copied.
	 * @param string $target Path to which copy is created.
	 * @return bool Was it possible to copy such the file?
	 */
	public function copy(string $path, string $target): bool
	{
		$src = $this->treat($path);
		$tgt = $this->treat($target);
		
		return $this->write($src, $this->read($tgt));
	}
	
	/**
	 * Returns metadata available for given path.
	 * @param string $path Target path for metadata request.
	 * @param string $data Specific data to be retrieved.
	 * @return mixed All metadata values or retrieved specific data.
	 */
	public function info(string $path = null, string $data = null)
	{
		if(!$this->has($path))
			return null;
		
		$stat = $this->archive->statName(
			$this->isFile($path) ? $path : $path.'/'
		);
		
		return is_null($data)
			? new Collection($stat)
			: ($stat[$data] ?? null);
	}
	
	/**
	 * Checks whether the given path is a directory.
	 * @param string $path Path to be checked.
	 * @return bool Is path a directory?
	 */
	public function isDir(string $path): bool
	{
		$tgt = $this->treat($path);
		return (bool)$this->archive->statName($tgt.'/');
	}
	
	/**
	 * Checks whether the given path is a file.
	 * @param string $path Path to be checked.
	 * @return bool Is path a file?
	 */
	public function isFile(string $path): bool
	{
		$tgt = $this->treat($path);
		return (bool)$this->archive->statName($tgt);
	}
	
	/**
	 * Creates a new directory into the disk.
	 * @param string $path Path of the directory to be created.
	 * @return bool Was the directory successfully created?
	 */
	public function mkdir(string $path): bool
	{
		$tgt = $this->treat($path);
		return $this->isdir($tgt) or $this->archive->addEmptyDir($tgt);
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
		
		return $this->archive->renameName($src, $tgt);
	}
	
	/**
	 * Lists all files and directories contained in the given path.
	 * @param string $dir Path to be listed.
	 * @return SequenceInterface All directory contents in the path.
	 */
	public function list(string $dir = null): SequenceInterface
	{
		$result = new Sequence;
		$dir = $this->treat($dir ?: '');
		$dirlen = strlen($dir);
		$this->reopen();
		
		for($i = 0; $i < $this->archive->numFiles; ++$i)
			$result->push($this->archive->statIndex($i));
		
		return $result->map(function($item) use($dir, $dirlen) {
			return substr($item['name'], 0, $dirlen) == $dir
				? $item['name']
				: false;
		})->filter();
	}
	
	/**
	 * Opens a file as a directly readable stream.
	 * @param string $filename File to be opened.
	 * @param string $mode Only reading is allowed for this disk.
	 * @return StreamInterface The directly readable file handler.
	 */
	public function open(string $filename, string $mode = 'r'): StreamInterface
	{
		$this->reopen();
		
		return $this->has($filename)
			? new Stream('zip://'.$this->archive->filename.'#'.$filename, 'r')
			: null;
	}
	
	/**
	 * Retrieves all contents from the given file.
	 * @param string $filename File to be read.
	 * @return string All file contents.
	 */
	public function read(string $filename)
	{
		$this->reopen();
		return $this->archive->getFromName($filename) ?: null;
	}
	
	/**
	 * Retrieves contents from a file and puts it into a stream.
	 * @param string $file Source file to be read.
	 * @param resource|Stream $stream Target stream to put content on.
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
	 * Removes a directory from disk.
	 * @param string $path Path to directory to be removed from disk.
	 * @return bool Was directory successfully removed?
	 */
	public function rmdir(string $path): bool
	{
		$this->reopen();
		$tgt = $this->treat($path).'/';
		$length = strlen($tgt);
		
		for($i = 0; $i < $this->archive->numFiles; ++$i)
			substr($this->archive->statIndex($i)['name'], 0, $length) === $tgt
				? $this->archive->deleteIndex($i)
				: (null);
		
		return $this->archive->deleteName($path);
	}
	
	/**
	 * Appends the content to a file, that will be created if needed.
	 * @param string $filename Target file path to be written.
	 * @param mixed $content Content to be written to path.
	 * @return int Number of written characters.
	 */
	public function update(string $filename, $content): int
	{
		$previous = $this->has($filename) ? $this->read($filename) : '';
		$this->remove($filename);
		
		return $this->write($filename, $previous.$content);
	}
	
	/**
	 * Retrieves content from stream and appends it to a file.
	 * @param resource|Stream $stream Source content is retrieved from.
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
		$tgt = $this->treat($filename);
		$dirname = self::dirname($tgt);
		
		if($dirname && !$this->has($dirname))
			$this->mkdir($dirname);
		
		$success = $this->archive->addFromString($tgt, $content);
		return $success ? strlen($content) : 0;
	}
	
	/**
	 * Retrieves content from stream and writes it to a file.
	 * @param resource|Stream $stream Stream content is retrieved from.
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
	 * Reopens the zip file. This is needed whenever we want to commit changes
	 * to the file. If the changes are not committed, the number of files, for
	 * instance, can be wrongly calculated.
	 */
	protected function reopen()
	{
		$path = $this->archive->filename;
		$this->archive->close();
		$this->archive->open($path);
		
		if(!is_null($this->password))
			$this->archive->setPassword($this->password);
	}
	
	/**
	 * Ensures that no file is treated like a directory.
	 * @param string $path File to have its path treated.
	 * @return string The treated file path.
	 */
	protected function treat(string $path): string
	{
		return rtrim($path, '/');
	}
	
	/**
	 * Returns the directory name of the given path.
	 * @param string $path The path to be analyzed.
	 * @return string The directory name of given path.
	 */
	protected static function dirname(string $path): string
	{
		$dirname = dirname($path);
		return $dirname === '.' ? '' : $dirname;
	}
	
}
