<?php
/**
 * Zettacast\HTTP\URL class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\HTTP;

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
	 * @var array Variables to be sent via the URL.
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
		$this->query = array_merge($this->query ?? [], $query);
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
		$data['host'] = $s['HTTP_HOST'] ?? $s['SERVER_NAME'] ?? '127.0.0.1';
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
		$this->scheme   = $data['scheme'] ?? 'http';
		$this->user     = $data['user'] ?? null;
		$this->host     = $data['host'] ?? null;
		$this->port     = $data['port'] ?? null;
		$this->segments = explode('/', $data['path'] ?? null);
		parse_str($data['query'] ?? null, $this->query);

		if(isset($data['pass']))
			$this->user .= ':'.$data['pass'];
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
