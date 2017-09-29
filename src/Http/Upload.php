<?php
/**
 * Zettacast\HTTP\Upload class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Http;

use Zettacast\Facade\File;
use Zettacast\Filesystem\Info;
use Zettacast\Collection\RecursiveCollection;

/**
 * This class stores information about an uploaded file.
 * @package Zettacast\HTTP
 * @version 1.0
 */
class Upload extends Info
{
	/**
	 * The error code related to the upload.
	 * @var int Error code related to upload.
	 */
	protected $error;
	
	/**
	 * The original name the user uses for the file.
	 * @var string The file's original name.
	 */
	protected $original;
	
	/**
	 * Lists all keys PHP creates when a file is uploaded.
	 * @var array List of uploaded file properties.
	 */
	private static $info = ['error', 'name', 'size', 'tmp_name', 'type'];
	
	/**
	 * This constructor simply sets all of the object's properties.
	 * @param string $tmpname The temporary name given to file.
	 * @param string $original The original name of the file.
	 * @param int $error The error code related to the upload.
	 */
	public function __construct(
		string $tmpname,
		string $original,
		int $error = null
	) {
		parent::__construct($tmpname);
		$this->original = $original;
		$this->error = $error ?: UPLOAD_ERR_OK;
	}
	
	/**
	 * Informs the error code sent with the file.
	 * @return int The error code.
	 */
	public function error() : int
	{
		return $this->error;
	}
	
	/**
	 * An uploaded file is usually put in a temporary directory. So, when this
	 * script ends, the file is not guaranteed to still exist. For that reason,
	 * a special method for moving the file is given.
	 * @param string $target The uploaded file's new location.
	 * @return bool Was the file successfully moved?
	 */
	public function move(string $target) : bool
	{
		if(!File::mkdir(dirname($target)))
			return false;
		
		if(!@move_uploaded_file($this->getPath(), $target))
			return false;
		
		@chmod($target, 0666 & ~umask());
		return true;
	}
	
	/**
	 * Informs the original name of the file, as informed by the user.
	 * @return string The name the user referred as to this file.
	 */
	public function original() : string
	{
		return $this->original;
	}
	
	/**
	 * Checks whether the file is a valid upload or not.
	 * @return bool Is the file valid?
	 */
	public function valid() : bool
	{
		$ok = $this->error == UPLOAD_ERR_OK;
		return $ok && is_uploaded_file($this->getPath());
	}
	
	/**
	 * Builds an instance from PHP raw files array.
	 * @param array $file Uploaded file information.
	 * @return Upload The created instance.
	 */
	public static function build(array $file) : self
	{
		$keys = array_keys($file);
		sort($keys);
		
		return $keys == self::$info
			? new self($file['name'], $file['tmp_name'], $file['error'])
			: null;
	}
	
	/**
	 * Receives, converts and returns a list of uploaded files.
	 * @return RecursiveCollection List of uploaded files.
	 */
	public static function capture() : RecursiveCollection
	{
		foreach($_FILES as $key => $info)
			$files[$key] = self::convert($info);
		
		return new RecursiveCollection($files ?? []);
	}
	
	/**
	 * Fixes a malformed PHP files array. It's safe to pass an already fixed
	 * array, as this method will simply return it unmodified.
	 * @param array $data Malformed file array to be fixed.
	 * @return array|self Fixed file array.
	 */
	protected static function convert(array $data)
	{
		$keys = array_keys($data);
		sort($keys);
		
		if($keys != self::$info)
			return $data;
		else if(!is_array($data['name']))
			return self::build($data);
			
		$files = [];
		
		foreach($data['name'] as $key => $value)
			$files[$key] = self::convert([
				'name'     => $data['name'][$key],
				'size'     => $data['size'][$key],
				'type'     => $data['type'][$key],
				'error'    => $data['error'][$key],
				'tmp_name' => $data['tmp_name'][$key]
			]);
		
		return $files;
	}
	
}
