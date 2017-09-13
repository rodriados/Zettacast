<?php
/**
 * Zettacast\HTTP\Header class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\HTTP;

use Zettacast\Collection\Recursive;

/**
 * This class is responsible for storing all request's headers.
 * @package Zettacast\HTTP
 * @version 1.0
 */
class Header
	extends Recursive
{
	/**
	 * Collection constructor. This constructor simply sets the data received
	 * as the data stored in collection.
	 * @param array|\Traversable $data Data to be stored.
	 */
	public function __construct($data = null)
	{
		parent::__construct([]);
		
		foreach($data as $key => $value)
			$this->set($key, $value);
	}
	
	/**
	 * Allows the object to be represented as a string.
	 * @return string String representation for this object.
	 */
	public function __toString() : string
	{
		if($this->empty())
			return (string)null;
		
		$headers = $this->all();
		$string = null;
		ksort($headers);
		
		foreach($headers as $key => $values)
			foreach($values as $value)
				$string .= sprintf(
					"%s: %s\r\n",
					self::format($key), $value
				);
		
		return $string;
	}
	
	/**
	 * Sets a value to the given key.
	 * @param mixed $key Key to created or updated.
	 * @param mixed $value Value to be stored in key.
	 * @return static Collection for method chaining.
	 */
	public function set($key, $value)
	{
		parent::set($key, toarray($value));
		return $this;
	}
	
	/**
	 * Formats a string to the header name style.
	 * @param string $key Server variable name.
	 * @return string Formatted header name.
	 */
	public static function format(string $key) : string
	{
		return str_replace(' ', '-', ucwords(strtolower(
			str_replace(['_', '-'], ' ', $key)
		)));
	}
	
	/**
	 * Creates a collection of all headers sent to the current request.
	 * @return static Collection of request headers.
	 */
	public static function capture()
	{
		$special = [
			'CONTENT_TYPE'   => 'Content-Type',
			'CONTENT_LENGTH' => 'Content-Length',
			'CONTENT_MD5'    => 'Content-Md5',
		];
		
		foreach($_SERVER as $key => $value)
			if(substr($key, 0, 5) == 'HTTP_')
				$header[self::format(substr($key, 5))] = $value;
			elseif(isset($special[$key]))
				$header[$special[$key]] = $value;
		
		if(!isset($header['Authorization'])) {
			if(isset($_SERVER['REDIRECT_HTTP_AUTORIZATION']))
				$header['Authorization'] =
					$_SERVER['REDIRECT_HTTP_AUTHORIZATION'];

			elseif(isset($_SERVER['PHP_AUTH_USER']))
				$header['Authorization'] =
					'Basic '.base64_encode($_SERVER['PHP_AUTH_USER'].':'.
					($_SERVER['PHP_AUTH_PW'] ?? ''));

			elseif(isset($_SERVER['PHP_AUTH_DIGEST']))
				$header['Authorization'] =
					$_SERVER['PHP_AUTH_DIGEST'];
		}
		
		return new static($header ?? []);
	}
	
}
