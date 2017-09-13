<?php
/**
 * Zettacast\HTTP\URI class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\HTTP;

use Zettacast\HTTP\Exception\InvalidURLException;

class URI
{
	protected $host;
	
	protected $port;
	
	protected $user;
	
	protected $pass;
	
	protected $segments;
	
	protected $query;
	
	protected $scheme;
	
	public function __construct(string $target, array $query = [])
	{
		if(!filter_var($target, FILTER_VALIDATE_URL))
			throw new InvalidURLException($target);
		
		$url = parse_url(filter_var($target, FILTER_SANITIZE_URL));
		$this->host = $url['host'] ?? config('app.url', '127.0.0.1');
		$this->port = $url['port'] ?? null;
		$this->user = $url['user'] ?? null;
		$this->pass = $url['pass'] ?? null;
		$this->scheme = $url['scheme'] ?? null;
		parse_str($url['query'] ?? null, $data);
		$path = $url['path'] ?? null;
		
		if(strpos($this->host, '/') !== false)
			list($this->host, $path) = explode('/', $this->host, 2);
		
		$this->query = array_merge($query, $data);
		$this->segments = explode('/', $path);
	}
	
	/**
	 * Captures the URL used for the current global request.
	 * @return static The URL instance related to the current request.
	 */
	public static function capture()
	{
		$secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';
		$protocol = $secure ? 'https' : 'http';
		$port = $_SERVER['SERVER_PORT'] ?? ($secure ? 443 : 80);
		$host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '127.0.0.1';
		$request = $_SERVER['REQUEST_URI'];
		
		$url = sprintf('%s://%s:%s%s', $protocol, $host, $port, $request);
		return new static($url, $_GET);
	}
	
}
