<?php
/**
 * Zettacast\FileSystem\File class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\FileSystem;

use Exception;
use Zettacast\FileSystem\Contract\Handler;

class File implements Handler
{
	protected $pointer;
	
	public function __construct($file, string $mode = 'r')
	{
		if(is_string($file))
			$this->pointer = fopen($file, $mode);
		
		elseif($file instanceof self)
			$this->pointer = $file->pointer;
		
		elseif(is_resource($file))
			$this->pointer = $file;
		
		else throw new Exception('Unable to open file "'.$file.'".');
	}
	
	public function __destruct()
	{
		fclose($this->pointer);
	}
	
	public function eof() : bool
	{
		return feof($this->pointer);
	}
	
	public function flush() : bool
	{
		return fflush($this->pointer);
	}
	
	public function getchar() : string
	{
		return fgetc($this->pointer);
	}
	
	public function lock(bool $share = false, bool $blocking = false) : bool
	{
		$lock = ($share ? LOCK_SH : LOCK_EX) | ($blocking ? 0 : LOCK_NB);
		return flock($this->pointer, $lock);
	}
	
	public function offset(int $offset) : bool
	{
		return (bool)fseek($this->pointer, $offset, SEEK_CUR);
	}
	
	public function printf(string $format, ...$vars) : int
	{
		return fprintf($this->pointer, $format, ...$vars);
	}
	
	public function read(int $length = null) : string
	{
		$this->rewind();
		return stream_get_contents($this->pointer, $length ?: -1);
	}
	
	public function readLine(int $length = null) : string
	{
		return fgets($this->pointer, $length ?: 8192);
	}
	
	public function readTo($target, int $length = -1)
	{
		if($target instanceof Handler)
			return $target->write($this->read($length));
		
		return stream_copy_to_stream($this->pointer, $target, $length);
	}
	
	public function rewind() : bool
	{
		return rewind($this->pointer);
	}
	
	public function scanf(string $format, &...$vars)
	{
		return fscanf($this->pointer, $format, ...$vars);
	}
	
	public function seek(int $offset) : bool
	{
		return (bool)fseek($this->pointer, $offset, SEEK_SET);
	}
	
	public function tell() : int
	{
		return ftell($this->pointer);
	}
	
	public function truncate(int $size) : bool
	{
		return ftruncate($this->pointer, $size);
	}
	
	public function unlock() : bool
	{
		return flock($this->pointer, LOCK_UN | LOCK_NB);
	}
	
	public function write(string $content, int $length = null) : int
	{
		return !is_null($length)
			? fwrite($this->pointer, $content, $length)
			: fwrite($this->pointer, $content);
	}
	
	public function writeFrom($source, int $length = null) : int
	{
		if($source instanceof Handler)
			return $this->write($source->read($length), $length);
		
		return stream_copy_to_stream($source, $this->pointer, $length);
	}
	
	public static function temp()
	{
		return new static('php://temp', 'w+b');
	}
	
}
