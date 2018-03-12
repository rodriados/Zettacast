<?php
/**
 * Zettacast\Filesystem\DiskInterface interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Filesystem;

use Zettacast\Stream\StreamInterface;
use Zettacast\Collection\SequenceInterface;

/**
 * The disk interface is responsible for exposing mandatory methods a
 * filesystem disk must have.
 * @package Zettacast\Filesystem
 * @version 1.0
 */
interface DiskInterface
{
	/**
	 * Checks whether a path exists in disk.
	 * @param string $path Path to check existance.
	 * @return bool Was path found?
	 */
	public function has(string $path): bool;
	
	/**
	 * Removes a file from disk.
	 * @param string $path Path to file to remove from disk.
	 * @return bool Was file or directory successfully removed?
	 */
	public function remove(string $path): bool;
	
	/**
	 * Creates a copy of a file in given destiny path.
	 * @param string $path File to copy.
	 * @param string $target Path to which copy is created.
	 * @return bool Was file successfully copied?
	 */
	public function copy(string $path, string $target): bool;
	
	/**
	 * Returns metadata available for given path.
	 * @param string $path Target path for metadata request.
	 * @param string $data Specific data to retrieve.
	 * @return mixed All metadata values or retrieved specific data.
	 */
	public function info(string $path = null, string $data = null);
	
	/**
	 * Checks whether given path is a directory.
	 * @param string $path Path to check whether it is a directory.
	 * @return bool Is path a directory?
	 */
	public function isdir(string $path): bool;
	
	/**
	 * Checks whether given path is a file.
	 * @param string $path Path to check whether it is a file.
	 * @return bool Is path a file?
	 */
	public function isfile(string $path): bool;
	
	/**
	 * Creates a new directory into disk.
	 * @param string $path Path of directory to create.
	 * @return bool Was directory successfully created?
	 */
	public function mkdir(string $path): bool;
	
	/**
	 * Moves given file or directory to another location.
	 * @param string $path Target path, that will be moved.
	 * @param string $newpath The new name for target file or directory.
	 * @return bool Was file or directory successfully moved?
	 */
	public function move(string $path, string $newpath): bool;
	
	/**
	 * Lists all files and directories contained in given path.
	 * @param string $dir Path to list.
	 * @return SequenceInterface All directory contents in path.
	 */
	public function list(string $dir = null): SequenceInterface;
	
	/**
	 * Opens a file as a directly editable stream.
	 * @param string $fname File to open.
	 * @param string $mode Reading/writing mode the file should open in.
	 * @return StreamInterface The directly editable file handler.
	 */
	public function open(string $fname, string $mode = 'r'): StreamInterface;
	
	/**
	 * Retrieves all contents from given file.
	 * @param string $filename File to read.
	 * @return string All file contents.
	 */
	public function read(string $filename);
	
	/**
	 * Retrieves contents from a file and puts it into a stream.
	 * @param string $file Source file to read.
	 * @param resource|StreamInterface $stream Target to put content on.
	 * @param int $length Maximum number of bytes to write to stream.
	 * @return int Length of data read from file.
	 */
	public function readto(string $file, $stream, int $length = null): int;
	
	/**
	 * Removes a directory from disk.
	 * @param string $path Path to directory to remove from disk.
	 * @return bool Was directory successfully removed?
	 */
	public function rmdir(string $path): bool;
	
	/**
	 * Appends content to a file, that will be created if needed.
	 * @param string $filename Target file path to be written.
	 * @param mixed $content Content to write to path.
	 * @return int Number of written characters.
	 */
	public function update(string $filename, $content): int;
	
	/**
	 * Retrieves content from stream and appends it to a file.
	 * @param resource|StreamInterface $stream Source to get content from.
	 * @param string $file Target file to be written to.
	 * @param int $length Maximum number of bytes to write to file.
	 * @return int Total length of data written to file.
	 */
	public function updatefrom($stream, string $file, int $length = null): int;
	
	/**
	 * Writes content to a file, that will be created if needed.
	 * @param string $filename Target file path to be written.
	 * @param mixed $content Content to write to path.
	 * @return int Number of written characters.
	 */
	public function write(string $filename, $content): int;
	
	/**
	 * Retrieves content from stream and writes it to a file.
	 * @param resource|StreamInterface $stream Stream to get content from.
	 * @param string $file Target file to be written to.
	 * @param int $length Maximum number of bytes to write to file.
	 * @return int Total length of data written to file.
	 */
	public function writefrom($stream, string $file, int $length = null): int;
}
