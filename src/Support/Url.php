<?php
/**
 * Zettacast\Support\Url class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Support;

use Zettacast\Collection\Collection;

/**
 * The universal resource locator class. This class is responsible for
 * identifying external or internally known resources. This object simply holds
 * the URL and is not responsible for making sense out of it.
 * @package Zettacast\Support
 * @version 1.0
 */
class Url
{
	/**
	 * The protocol scheme used in the URL.
	 * @var string Protocol scheme.
	 */
	protected $scheme;
	
	/**
	 * The user credentials information, sent via URL. Although supported,
	 * this component should not contain password information.
	 * @var string The user credentials.
	 */
	protected $userinfo;
	
	/**
	 * Target host to which the URL is related.
	 * @var string Host related to URL.
	 */
	protected $host;
	
	/**
	 * The port used in host server.
	 * @var int URL connection port.
	 */
	protected $port;
	
	/**
	 * The URL's path component.
	 * @var string URL path segment.
	 */
	protected $path;
	
	/**
	 * Query variables related to this URL.
	 * @var Collection Variables to be sent via URL.
	 */
	protected $query;
	
	/**
	 * The URL's fragment component.
	 * @var string URL fragment component.
	 */
	protected $fragment;
	
	/**
	 * URL Constructor.
	 * @param string|array $url URL data to be stored in this object.
	 * @param array $query Query variables to be related to given URL.
	 */
	public function __construct($url, $query = [])
	{
		is_array($url) ? $this->initialize($url) : $this->parse($url);
		$this->query = with(new Collection($this->query))->merge($query);
	}
	
	/**
	 * Treats the process of cloning this object. This is needed so internal
	 * objects are not shared between different instances.
	 */
	public function __clone()
	{
		$this->query = clone $this->query;
	}
	
	/**
	 * Allows the object to be represented as a string.
	 * @return string String representation for this object.
	 */
	public function __toString(): string
	{
		return $this->getFull();
	}
	
	/**
	 * Gives access to the URL's scheme component.
	 * @return string The URL scheme component.
	 */
	public function getScheme()
	{
		return $this->scheme ?: null;
	}
	
	/**
	 * Rebuilds the authority component of the URL and returns it.
	 * @return string The authority component.
	 */
	public function getAuthority()
	{
		$full = $this->getHost();
		
		if($data = $this->getUserInfo())
			$full = $this->userinfo.'@'.$full;
		
		if($data = $this->getPort())
			$full .= ':'.$data;
		
		return $full ?: null;
	}
	
	/**
	 * Gives access to the URL's authority's userinfo component.
	 * @return int The current userinfo informed by the URL.
	 */
	public function getUserInfo()
	{
		return $this->userinfo ?: null;
	}
	
	/**
	 * Gives access to the URL's authority's host component.
	 * @return int The current host informed by the URL.
	 */
	public function getHost()
	{
		return $this->host ?: null;
	}
	
	/**
	 * Gives access to the URL's authority's port component.
	 * @return int The current port informed by the URL.
	 */
	public function getPort()
	{
		return $this->port ?: null;
	}
	
	/**
	 * Gives access to the URL's path component.
	 * @return int The current path informed by the URL.
	 */
	public function getPath()
	{
		return $this->path ?: null;
	}
	
	/**
	 * Gives access to the URL's query component.
	 * @return int The current query informed by the URL.
	 */
	public function getQuery()
	{
		return !$this->query->empty()
			? http_build_query($this->query->all())
			: null;
	}
	
	/**
	 * Gives access to the URL's fragment component.
	 * @return int The current query informed by the URL.
	 */
	public function getFragment()
	{
		return $this->fragment ?: null;
	}
	
	/**
	 * Rebuilds the full URL.
	 * @return string The full URL with all of its known components.
	 */
	public function getFull(): string
	{
		$full = $this->path;
		
		if($comp = $this->getAuthority())
			$full = '//'.$comp.$full;
		
		if($comp = $this->getScheme())
			$full = $comp.':'.$full;
		
		if($comp = $this->getQuery())
			$full .= '?'.$comp;
		
		if($comp = $this->getFragment())
			$full .= '#'.$comp;
		
		return $full;
	}
	
	/**
	 * Initializes the object's properties based on a given URL array data.
	 * @param array $data Data to be inserted into the object.
	 */
	protected function initialize(array $data)
	{
		foreach($data as &$value)
			$value = $value ?: null;
		
		$this->scheme   = $data['scheme'] ?? null;
		$this->userinfo = $data['userinfo'] ?? null;
		$this->host     = $data['host'] ?? null;
		$this->port     = (int)($data['port'] ?? 0) ?: null;
		$this->path     = $data['path'] ?? null;
		$this->fragment = $data['fragment'] ?? null;
		parse_str($data['query'] ?? null, $this->query);
	}
	
	/**
	 * Parses the URL passed as a string and builds the object's properties.
	 * @param string $url URL to be parsed.
	 * @throws \Exception The given string is not a valid URL.
	 */
	protected function parse(string $url)
	{
		$valid = preg_match('!^'.
			'(?>(?<scheme>[a-z][^:/?#]*):)?'.
			'(?>//(?<authority>'.
				'(?:(?<userinfo>[^@]*)@)?'.
				'(?<host>\[[^\]]+\]|[^:/?#]*)'.
				'(?::(?<port>[0-9]*))?'.
			'))?'.
			'(?<path>(?(2)/)[^?#]*)?'.
			'(?:\?(?<query>[^#]*))?'.
			'(?:#(?<fragment>.*))?'.
			'$!i', $url, $match
		);
		
		if(!$valid)
			throw new \Exception('The URL \''.$url.'\' is invalid!');
			
		$this->initialize($match);
	}
	
}
