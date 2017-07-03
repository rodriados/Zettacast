<?php
/**
 * Zettacast\FileSystem\Contract\Driver interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\FileSystem\Contract;

use Zettacast\Collection\Contract\Collection;

/**
 * @todo Rethink how drivers work, they should be more lower level and have
 * @todo methods such as streamOpen, fileStat and maybe some others.
 * The Driver interface is responsible for exposing mandatory methods a
 * FileSystem driver must have.
 * @package Zettacast\FileSystem\Contract
 */
interface Driver
{
	/**
	 * Creates a copy of a file in the given destiny path.
	 * @param string $path File to be copied.
	 * @param string $target Path to which copy is created.
	 * @return bool Was it possible to copy such a file?
	 */
	public function copy(string $path, string $target) : bool;
	
	/**
	 * Checks whether a path exists in the driver.
	 * @param string $path Path to be checked.
	 * @return bool Was the path found?
	 */
	public function has(string $path) : bool;
	
	/**
	 * Checks whether the given path is a directory.
	 * @param string $path Path to be checked.
	 * @return bool Is path a directory?
	 */
	public function isdir(string $path) : bool;
	
	/**
	 * Checks whether the given path is a file.
	 * @param string $path Path to be checked.
	 * @return bool Is path a file?
	 */
	public function isfile(string $path) : bool;
	
	/**
	 * Lists all files and directories contained in the given path.
	 * @param string $dir Path to be listed.
	 * @return array All directory contents in the path.
	 */
	public function list(string $dir = null) : array;
	
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
	) : Collection;
	
	/**
	 * Creates a new directory into the driver.
	 * @param string $path Path of the directory to be created.
	 * @param int $perms Permission to be given to the new directory.
	 * @return bool Was the directory successfully created?
	 */
	public function mkdir(string $path, int $perms = 0777) : bool;
	
	/**
	 * Opens a file as a directly editable object.
	 * @param string $filename File to be opened.
	 * @param string $mode Reading/writing mode the file should be opened in.
	 * @return Handler The directly editable file handler.
	 */
	public function open(string $filename, string $mode = 'r') : Handler;
	
	/**
	 * Edits the permission information of the given path.
	 * @param string $path Path to be editted.
	 * @param int $perms Permission to be set to given path.
	 * @return bool Was permission successfully executed?
	 */
	public function permission(string $path, int $perms = 0777) : bool;
	
	/**
	 * Retrieves all contents from the given file.
	 * @param string $filename File to be read.
	 * @return string All file contents.
	 */
	public function read(string $filename) : string;
	
	/**
	 * Reads all of file contents to a stream or target file.
	 * @param string $filename File to be read.
	 * @param mixed $target Target to which file contents is put onto.
	 * @return mixed Return value is not defined.
	 */
	public function readTo(string $filename, $target);
	
	/**
	 * Removes a file from driver.
	 * @param string $path Path to file to be removed from driver.
	 * @return bool Was file successfully removed?
	 */
	public function remove(string $path) : bool;
	
	/**
	 * Renames given file or directory and moves it, if needed.
	 * @param string $path Target path, that will be renamed.
	 * @param string $newpath The new name for target file or directory.
	 * @return bool Was the file or directory successfully renamed?
	 */
	public function rename(string $path, string $newpath) : bool;
	
	/**
	 * Removes a directory from driver.
	 * @param string $path Path to directory to be removed from driver.
	 * @return bool Was directory successfully removed?
	 */
	public function rmdir(string $path) : bool;
	
	/**
	 * Writes the content to a file, that will be created if needed.
	 * @param string $filename Target file path to be written.
	 * @param mixed $content Content to be written to path.
	 * @return int Number of written characters.
	 */
	public function write(string $filename, $content) : int;
	
	/**
	 * Writes the content of a resource or File to a file located in the
	 * driver, that will be created if needed.
	 * @param string $filename Target file path to be written.
	 * @param mixed $source Source to retrieve file content's from.
	 * @return int Number of written characters.
	 */
	public function writeFrom(string $filename, $source) : int;

}
