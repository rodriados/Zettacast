<?php
/**
 * Filesystem façade file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Facade;

use Zettacast\Helper\FacadeAbstract;
use Zettacast\Stream\StreamInterface;
use Zettacast\Collection\SequenceInterface;
use Zettacast\Filesystem\Filesystem as baseclass;

/**
 * Zettacast's Filesystem façade class.
 * This class exposes package:filesystem\Filesystem methods to external usage.
 * @method static bool chmod(string $path, int $perms = 0777)
 * @method static bool copy(string $path, string $target)
 * @method static bool has(string $path)
 * @method static mixed info(string $path = null, string $data = null)
 * @method static bool isdir(string $path)
 * @method static bool isfile(string $path)
 * @method static SequenceInterface list(string $dir = null)
 * @method static bool mkdir(string $path)
 * @method static bool move(string $path, string $newpath)
 * @method static StreamInterface open(string $filename, string $mode = 'r')
 * @method static string read(string $filename)
 * @method static int readto(string $file, $stream, int $length = null)
 * @method static bool remove(string $path)
 * @method static bool rmdir(string $path)
 * @method static int update(string $filename, $content)
 * @method static int updatefrom($stream, string $file, int $length = null)
 * @method static int write(string $filename, $content)
 * @method static int writefrom($stream, string $file, int $length = null)
 * @version 1.0
 */
final class File extends FacadeAbstract
{
	/**
	 * Checks whether a path exists in the filesystem.
	 * @param string $path Path to check existance.
	 * @return bool Was the path found?
	 */
	public static function exists(string $path): bool
	{
		return self::i()->has($path);
	}
	
	/**
	 * Informs what the façaded object accessor is, allowing it to be further
	 * used when calling façaded methods.
	 * @return mixed Façaded object accessor.
	 */
	protected static function accessor()
	{
		return baseclass::class;
	}
}
