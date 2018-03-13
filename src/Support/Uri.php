<?php
/**
 * Zettacast\Support\Uri class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Support;

use Zettacast\Collection\Collection;

/**
 * The universal resource identification class. This class is responsible for
 * identifying external or internally known resources. This object simply holds
 * the URI and is not responsible for making sense out of it.
 * @package Zettacast\Support
 * @version 1.0
 */
class Uri
{
	/**
	 * The protocol scheme used in the URI.
	 * @var string Protocol scheme.
	 */
	protected $scheme;
	
	/**
	 * The user credentials information, sent via URI. Although supported,
	 * this component should not contain password information.
	 * @var string The user credentials.
	 */
	protected $userinfo;
	
	/**
	 * Target host to which the URI is related.
	 * @var string Host related to URI.
	 */
	protected $host;
	
	/**
	 * The port used in host server.
	 * @var int URI connection port.
	 */
	protected $port;
	
	/**
	 * The URI's path component.
	 * @var string URI path segment.
	 */
	protected $path;
	
	/**
	 * Query variables related to this URI.
	 * @var Collection Variables to be sent via URI.
	 */
	protected $query;
	
	/**
	 * The URI's fragment component.
	 * @var string URI fragment component.
	 */
	protected $fragment;
	
	/**
	 * Uri constructor.
	 * Parses the resource locator and builds up the object.
	 * @param string|array $url URI data to store in this object.
	 * @param array $query Query variables to relate to given URI.
	 */
	public function __construct($url, $query = [])
	{
		is_array($url)
			? $this->initialize($url)
			: $this->parse($url);
		
		$this->query = $this->query->merge($query);
	}
	
	/**
	 * Uri clone magic method.
	 * Treats the process of cloning this object. This is needed so internal
	 * objects are not shared between different instances.
	 */
	public function __clone()
	{
		$this->query = clone $this->query;
	}
	
	/**
	 * Uri string representation magic method.
	 * Allows the object to be represented as a string.
	 * @return string String representation for this object.
	 */
	public function __toString(): string
	{
		return $this->full();
	}
	
	/**
	 * Uri access property magic method.
	 * @param string $name The name of property to access.
	 * @return mixed The property value.
	 */
	public function __get(string $name)
	{
		return isset($this->$name)
			? $this->$name
			: null;
	}
	
	/**
	 * Rebuilds the full URI.
	 * @return string The full URI with all of its known components.
	 */
	public function full(): string
	{
		$auth = $this->host;
		$full = $this->path;
		$query = !$this->query->empty()
			? http_build_query($this->query->raw())
			: null;
		
		$auth = $this->userinfo ? $this->userinfo . '@' . $auth : $auth;
		$auth = $this->port     ? $auth . ':' . $this->port     : $auth;
		$full = $auth           ? '//' . $auth . $full          : $full;
		$full = $this->scheme   ? $this->scheme . ':' . $full   : $full;
		$full = $query          ? $full . '?' . $query          : $full;
		$full = $this->fragment ? $full . '#' . $this->fragment : $full;
		
		return $full;
	}
	
	/**
	 * Gives access to a single path segment.
	 * @param int $index The segment index to access.
	 * @return string The segment content.
	 */
	public function segment(int $index): ?string
	{
		$segment = explode('/', $this->path);
		$segment[0] = $this->host;
		
		return $segment[$index] ?? null;
	}
	
	/**
	 * Initializes the object's properties based on a given URI array data.
	 * @param array $data Data to be inserted into the object.
	 */
	protected function initialize(array $data)
	{
		foreach($data as &$value)
			$value = $value ?: null;
		
		$this->scheme   = $data['scheme'] ?? null;
		$this->userinfo = $data['userinfo'] ?? null;
		$this->host     = $data['host'] ?? null;
		$this->port     = (int)($data['port'] ?? null);
		$this->path     = $data['path'] ?? null;
		$this->fragment = $data['fragment'] ?? null;
		
		parse_str($data['query'] ?? null, $query);
		$this->query = new Collection($query);
	}
	
	/**
	 * Parses the URI passed as a string and builds the object's properties.
	 * @param string $url URI to be parsed.
	 * @throws \Exception The given string is not a valid URI.
	 */
	protected function parse(string $url)
	{
		$regex = '!^'.
			'(?>(?<scheme>[a-z][^:/?#]*):)?'.
			'(?>//(?<authority>'.
				'(?:(?<userinfo>[^@]*)@)?'.
				'(?<host>\[[^\]]+\]|[^:/?#]*)'.
				'(?::(?<port>[0-9]*))?'.
			'))?'.
			'(?<path>(?(2)/)[^?#]*)?'.
			'(?:\?(?<query>[^#]*))?'.
			'(?:#(?<fragment>.*))?'.
			'$!i';

		if(!preg_match($regex, $url, $match))
			throw UriException::invalid($url);
		
		$this->initialize($match);
	}
}
