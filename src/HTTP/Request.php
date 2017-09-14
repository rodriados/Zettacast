<?php
/**
 * Zettacast\HTTP\Request class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\HTTP;

use Zettacast\Collection\Collection;
use Zettacast\Contract\HTTP\Request as RequestContract;

class Request
	implements RequestContract
{
	protected $url;
	
	protected $cookie;
	
	protected $files;
	
	protected $header;
	
	protected $content;
	
	protected $server;
	
	protected $method;
	
	public function __construct(string $method, $url, array $content = [])
	{
		$this->method = $method;
		$this->content = new Collection($content);
		$this->url = !$url instanceof URL ? new URL($url) : $url;
		
		$this->files = null;
		$this->header = null;
		$this->cookie = null;
		$this->server = null;
	}
	
	/**
	 * Captures all values related to the current global request.
	 * @todo All capture methods are ready.
	 * @return Request The instance created from global request.
	 */
	public static function capture() : Request
	{
		$header = Header::capture();
		$server = new Collection($_SERVER);
		$ctype = $header->get('Content-Type');
		$method = $server->get('REQUEST_METHOD', 'GET');
		
		if(strpos($ctype, 'application/x-www-form-urlencoded') === 0
		   && in_array($method, ['PUT', 'DELETE', 'PATCH']))
			parse_str(file_get_contents('php://input'), $data);
		
		$request = new static($method, URL::capture(), $data ?? $_POST);
		$request->files = Upload::capture();
		$request->cookie = new Collection($_COOKIE);
		$request->header = $header;
		$request->server = $server;
		return $request;
	}
	
}
