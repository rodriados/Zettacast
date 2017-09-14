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
	 * User authentication data sent in URL.
	 * @var string User data sent in URL.
	 */
	protected $user;
	
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
	 * Path string segments, index starting at 1.
	 * @var string[] URL path segments.
	 */
	protected $segments;
	
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
		$url  = $this->scheme . '://';
		$url .= $this->user ? $this->user . '@' : null;
		$url .= $this->host() . $this->path();
		$url .= $query ? '?'. http_build_query($query) : null;
		return $url;
	}
	
	/**
	 * Informs URL's host name. Appends the port if it is not the default one.
	 * @return string URL's host name and port.
	 */
	public function host() : string
	{
		$secure = $this->scheme == 'https';
		$host = $this->host ?? config('app.url', 'localhost');
		$port = $this->port;
		
		return $port && ($secure && $port != 443 || !$secure && $port != 80)
			? $host.':'.$port
			: $host;
	}
	
	/**
	 * Informs URL's full path part.
	 * @return string URL's path.
	 */
	public function path() : string
	{
		$path = implode('/', $this->segments);
		return '/'.$path;
	}
	
	/**
	 * Retrieves a segment from the URL's path, or all of them.
	 * @param int $index Segment index to be retrieved.
	 * @return string|string[] The requested segment or all of them.
	 */
	public function segment(int $index = null)
	{
		return !is_null($index)
			? ($this->segments[$index - 1] ?? null)
			: $this->segments;
	}
	
	/**
	 * Retrieves a query variable out of the URL.
	 * @param string $key Target key to be retrieved.
	 * @return mixed|Collection The requested variable or the whole collection.
	 */
	public function query(string $key = null)
	{
		return !is_null($key)
			? $this->query->get($key)
			: $this->query;
	}
	
	/**
	 * Retrieves user information from the URL.
	 * @return string User information contained in the URL.
	 */
	public function user()
	{
		return $this->user;
	}
	
	/**
	 * Captures the URL used for the current global request.
	 * @return static The URL instance related to the current request.
	 */
	public static function capture()
	{
		$s = $_SERVER;
		$secure = !empty($s['HTTPS']) && $s['HTTPS'] !== 'off';
		
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
		$this->scheme   = $data['scheme'] ?: 'http';
		$this->user     = rawurldecode($data['user'] ?? null) ?: null;
		$this->host     = rawurldecode($data['host'] ?? null) ?: null;
		$this->port     = (int)($data['port'] ?? null);
		$this->segments = explode('/', trim($data['path'] ?? null, '/'));
		parse_str($data['query'] ?? null, $this->query);

		if(isset($data['pass']))
			$this->user .= ':' . rawurldecode($data['pass']);
	}
	
	/**
	 * Parses a URL passed as a string and builds the object's properties.
	 * @param string $url URL to be parsed.
	 * @throws InvalidURLException The given string is not a valid URL.
	 */
	protected function parse(string $url)
	{
		if(!preg_match('!^'.
			'(?:(?<scheme>[^:]+)://)?'.            # Group 1: Scheme
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
