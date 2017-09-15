<?php
/**
 * Zettacast\HTTP\URL class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\HTTP;

use Zettacast\Collection\Collection;
use Zettacast\HTTP\Exception\InvalidURLException;

/**
 * This class is responsible for storing and dealing with URL-related data.
 * @property string $scheme Protocol scheme.
 * @property string $user User data sent in URL.
 * @property string $pass User password sent via URL.
 * @property string $host Host related to URL.
 * @property int $port URL connection port.
 * @property string $path URL path segment.
 * @property Collection $query Variables to be sent via the URL.
 * @package Zettacast\HTTP
 * @version 1.0
 */
class URL
{
	/**
	 * The protocol scheme used in the URL.
	 * @var string Protocol scheme.
	 */
	protected $scheme;
	
	/**
	 * User identification data sent in URL.
	 * @var string User data sent in URL.
	 */
	protected $user;
	
	/**
	 * User password authentication sent via URL.
	 * @var string User password sent via URL.
	 */
	protected $pass;
	
	/**
	 * Target host to which the URL is related. If none, the URL is internal.
	 * @var string Host related to URL.
	 */
	protected $host;
	
	/**
	 * The port used in host server.
	 * @var string URL connection port.
	 */
	protected $port;
	
	/**
	 * The request path.
	 * @var string URL path segment.
	 */
	protected $path;
	
	/**
	 * Query variables related to this URL.
	 * @var Collection Variables to be sent via the URL.
	 */
	protected $query;
	
	/**
	 * URL Constructor.
	 * @param array|string $data URL data to be stored in this object.
	 * @param array $query Query variables to be related to given URL.
	 */
	public function __construct($data, array $query = [])
	{
		is_string($data) ? $this->parse($data) : $this->initialize($data);
		$this->query = with(new Collection($this->query ?? []))->merge($query);
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
	 * Gives read-only access to the URL's parts.
	 * @param string $property Name of property to be accessed.
	 * @return string|int|Collection The value of the required property.
	 */
	public function __get(string $property)
	{
		$list = ['scheme', 'user', 'pass', 'host', 'port', 'path', 'query'];
		
		return in_array($property, $list)
			? $this->$property ?? null
			: null;
	}
	
	/**
	 * Allows the object to be represented as a string.
	 * @return string String representation for this object.
	 */
	public function __toString() : string
	{
		return $this->full();
	}
	
	/**
	 * Builds the full URL.
	 * @return string Full URL.
	 */
	public function full() : string
	{
		$query = $this->query->all();
		$secure = $this->scheme == 'https';
		$host = $this->host ?: config('app.url', 'localhost');
		$port = $this->port;
		
		if($port && ($secure && $port != 443 || !$secure && $port != 80))
			$host .= ':' . $port;
		
		$scheme = $this->scheme . '://';
		$user   = $this->user ?: null;
		$pass   = $this->pass ? ':' . $this->pass : null;
		$user   = $user ? $user . $pass . '@' : null;
		$path   = '/' . trim($this->path, '/');
		$query  = $query ? '?'. http_build_query($query) : null;

		return $scheme . $user . $host . $path . $query;
	}
	
	/**
	 * Retrieves a segment from the URL's path, or all of them.
	 * @param int $index Segment index to be retrieved.
	 * @return string|string[] The requested segment or all of them.
	 */
	public function segment(int $index = null)
	{
		static $segments = null;
		
		if(is_null($segments) || $segments[0] != $this->path) {
			$segments = explode('/', '/'.trim($this->path, '/'));
			$segments[0] = $this->path;
		}
		
		return !is_null($index)
			? ($segments[$index] ?? null)
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
	
	/**
	 * Initializes the object's properties based on a given URL array data.
	 * @param array $data Data to be inserted into the object.
	 */
	protected function initialize(array $data)
	{
		$split = explode(':', rawurldecode($data['user'] ?? null), 2);
		
		$this->scheme = $data['scheme'] ?: 'http';
		$this->user   = $split[0] ?: null;
		$this->pass   = $split[1] ?? null;
		$this->host   = rawurldecode($data['host'] ?? null) ?: null;
		$this->port   = (int)($data['port'] ?? null);
		$this->path   = '/'.trim($data['path'] ?? null, '/');
		parse_str($data['query'] ?? null, $this->query);

		if(isset($data['pass']))
			$this->pass = rawurldecode($data['pass']);
	}
	
	/**
	 * Parses a URL passed as a string and builds the object's properties.
	 * @param string $url URL to be parsed.
	 * @throws InvalidURLException The given string is not a valid URL.
	 */
	protected function parse(string $url)
	{
		if(!preg_match('!^'.
			'(?:(?<scheme>https?)://)?'.           # Group 1: Scheme
			'(?:(?<user>[^@]+)@)?'.                # Group 2: User ID
		    '(?:(?<host>[^:/?#]+|\[[0-9a-f:]+\])'. # Group 3: Hostname
		    '(?::(?<port>[0-9]+))?)?'.             # Group 4: Host port
		    '(?<path>/[^?]*)*'.                    # Group 5: Path
		    '(?:\?(?<query>[^#]+))?'.              # Group 6: Query
		    '(?:#.+)?$!',                          # Group 7: Fragment
			$url, $match
		)) {
			throw new InvalidURLException($url);
		}
		
		$this->initialize($match);
	}
	
}
