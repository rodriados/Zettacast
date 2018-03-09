<?php
/**
 * Zettacast\Http\Url class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Http;

use Zettacast\Support\Uri;

/**
 * This class is responsible for storing and dealing with all HTTP URLs.
 * @package Zettacast\Http
 * @version 1.0
 */
class Url extends Uri
{
	protected $secure;
	
	/**
	 * URL Constructor.
	 * @param array|string $data URL data to be stored in this object.
	 * @param array $query Query variables to be related to given URL.
	 * @throws \Exception The given scheme is not acceptable.
	 */
	public function __construct($data, array $query = [])
	{
		parent::__construct($data, $query);
		
		if(isset($this->scheme) && strpos($this->scheme, 'http') === false)
			throw new \Exception('Only HTTP URLs are accepted!');
	}
	
	/**
	 * Checks whether the stored URL uses a secure scheme or not.
	 * @return bool Is the URL secure?
	 */
	public function isSecure(): bool
	{
		return $this->getScheme() == 'https';
	}
	
	/**
	 * Gives access to the URI's authority's port component.
	 * @return int The current port informed by the URI.
	 */
	public function getPort()
	{
		return $this->isSecure() && $this->port == 443 || $this->port == 80
			? null
			: $this->port;
	}
	
	/**
	 * Retrieves a segment from the URL's path, or all of them.
	 * @param int $index Segment index to be retrieved.
	 * @return string|string[] The requested segment or all of them.
	 */
	public function getSegment(int $index = null)
	{
		static $segments = null;
		
		if(is_null($segments) || $segments[0] != $this->path) {
			$segments = explode('/', $this->path);
			$segments[0] = $this->path;
		}
		
		return !is_null($index) && isset($segments[$index])
			? $segments[$index]
			: $segments;
	}
	
	/**
	 * Captures the URL used for the current global request.
	 * @return static The URL instance related to the current request.
	 */
	public static function capture()
	{
		$s = $_SERVER;
		$secure = !empty($s['HTTPS']) && $s['HTTPS'] != 'off';
		
		$data['scheme'] = $secure ? 'https' : 'http';
		$data['host'] = $s['HTTP_HOST'] ?? $s['SERVER_NAME'] ?? 'localhost';
		$data['port'] = $s['SERVER_PORT'] ?? ($secure ? 443 : 80);
		$data['path'] = explode('?', $s['REQUEST_URI'], 2)[0];
		
		return new static($data, $_GET);
	}
	
}
