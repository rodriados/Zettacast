<?php
/**
 * Zettacast\Stream\Uri class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Stream;

/**
 * The universal resource identification class. This class is responsible for
 * identifying external or internally known resources. This object simply holds
 * the URI and is not responsible for making sense out of it.
 * @package Zettacast\Stream
 * @version 1.0
 */
class Uri
{
	/**
	 * The URI components list. This variable lists all existing components in
	 * an URI. Any other value besides these should and will be ignored when
	 * initializing an URI.
	 * @var string[] List of URI components.
	 */
	protected const COMPONENT = [
		'scheme', 'userinfo', 'host', 'port', 'path', 'query', 'fragment',
	];
	
	/**
	 * The URI components regular expressions. This property defines how the
	 * URI's fieds must be formatted.
	 * @var string[] List of URI components regular expression.
	 */
	protected const RGX = [
		'scheme'   => '[a-z][a-z0-9.+-]*',
		'userinfo' => '[^@]*',
		'host'     => '[^\[\]:/?#]*|\[[0-9a-fv:.]+\]',
		'port'     => '[0-9]*',
		'path'     => '/|/?[^/?#]+(?:/[^/?#]*)*',
		'query'    => '[^#]*',
		'fragment' => '.*'
	];
	
	/**
	 * The URI scheme.
	 * @var string The URI scheme.
	 */
	protected $scheme;
	
	/**
	 * The URI user information.
	 * @var string The URI user information.
	 */
	protected $userinfo;
	
	/**
	 * The URI host.
	 * @var string The URI host.
	 */
	protected $host;
	
	/**
	 * The URI connection port.
	 * @var int The URI connection port.
	 */
	protected $port;
	
	/**
	 * The URI path.
	 * @var string The URI path.
	 */
	protected $path;
	
	/**
	 * The URI query variables.
	 * @var string[]|string The URI query variables.
	 */
	protected $query;
	
	/**
	 * The URI fragment.
	 * @var string The URI fragment.
	 */
	protected $fragment;
	
	/**
	 * Uri constructor.
	 * Parses the resource locator and builds up the object.
	 * @param string|array $uri URI data to store in this object.
	 * @param array|\Traversable $query Query variables to relate to given URI.
	 */
	public function __construct($uri, $query = [])
	{
		is_string($uri)
			? $this->parse($uri)
			: $this->initialize(toarray($uri));
		
		$this->port = ((int)$this->port) ?: null;
	}
	
	/**
	 * Uri string representation magic method.
	 * Allows the object to be represented as a string.
	 * @return string String representation for this object.
	 */
	public function __tostring(): string
	{
		return $this->full();
	}
	
	/**
	 * Checks whether the stored URI is absolute.
	 * @return bool Is the URI absolute?
	 */
	public function absolute(): bool
	{
		return $this->scheme()
		       || !$this->authority() && $this->full()[0] == '/';
	}
	
	/**
	 * Checks whether the stored URI is relative.
	 * @return bool Is the URI relative?
	 */
	public function relative(): bool
	{
		return !$this->absolute();
	}
	
	/**
	 * Retrieves the URI scheme.
	 * @return string The URI scheme.
	 */
	public function scheme(): ?string
	{
		return $this->scheme ?: null;
	}
	
	/**
	 * Retrieves the URI user information.
	 * @return string The URI user information.
	 */
	public function userinfo(): ?string
	{
		return $this->userinfo ?: null;
	}
	
	/**
	 * Retrieves the URI host.
	 * @return string The URI host.
	 */
	public function host(): ?string
	{
		return $this->host ?: null;
	}
	
	/**
	 * Retrieves the URI port.
	 * @return int The URI port.
	 */
	public function port(): ?int
	{
		return $this->port ?: null;
	}
	
	/**
	 * Retrieves the URI authority.
	 * @return string The URI authority.
	 */
	public function authority(): ?string
	{
		$auth = $this->host();
		$auth = ($cache = $this->userinfo()) ? $cache . '@' . $auth  : $auth;
		$auth = ($cache = $this->port())     ? $auth  . ':' . $cache : $auth;
		
		return $auth;
	}
	
	/**
	 * Retrieves the URI path.
	 * @return string The URI path.
	 */
	public function path(): ?string
	{
		return $this->path ?: null;
	}
	
	/**
	 * Retrieves the URI query.
	 * @return string[] The URI query.
	 */
	public function query(): ?array
	{
		if(!is_null($this->query) && is_string($this->query))
			$this->query = self::decode($this->query);
		
		return $this->query;
	}
	
	/**
	 * Retrieves the URI query as a string.
	 * @return string The URI query string.
	 */
	public function querystr(): ?string
	{
		return !is_null($this->query) && !is_string($this->query)
			? self::encode($this->query)
			: $this->query;
	}
	
	/**
	 * Retrieves the URI fragment.
	 * @return string The URI fragment.
	 */
	public function fragment(): ?string
	{
		return $this->fragment ?: null;
	}
	
	/**
	 * Rebuilds the full URI.
	 * @return string The full URI with all of its known components.
	 */
	public function full(): string
	{
		$full[] = ($p = $this->scheme())    ? $p . ':'               : null;
		$full[] = ($p = $this->authority()) ? '//' . $p              : null;
		$full[] = ($p = $this->path())      ? self::normalize($p)    : null;
		$full[] = ($p = $this->query())     ? '?' . self::encode($p) : null;
		$full[] = ($p = $this->fragment())  ? '#' . $p               : null;
		
		return implode('', $full);
	}
	
	/**
	 * Gives access to a single path segment. The segments' indeces start at
	 * zero, unless URI has a authority part, in which case the first segment
	 * is empty in conformity with RFC3986.
	 * @param int $index The segment index to access.
	 * @return string The segment content.
	 */
	public function segment(int $index): ?string
	{
		static $segments;
		
		if(!isset($segments))
			$segments = explode('/', $this->path());
		
		return $segments[$index] ?? null;
	}
	
	/**
	 * Transforms given URI as a reference using the instanciated object as a
	 * base for the transformation. This method executes reference
	 * transformation in conformity with RFC3986.
	 * @param string|array|Uri $ref The reference to be transformed.
	 * @return self The transformed reference.
	 */
	public function reference($ref)
	{
		$tgt = [];
		$copyref = false;
		
		if(!$ref instanceof Uri)
			$ref = new self($ref);
		
		foreach(['scheme', 'authority', 'path', 'query', 'fragment'] as $comp) {
			$copyref = $copyref || $ref->$comp();
			$tgt[$comp] = $copyref ? $ref->$comp() : $this->$comp();
		}
		
		if(!$ref->scheme() && !$ref->authority() && $ref->path()) {
			if($tgt['path'][0] != '/')
				$tgt['path'] =
					substr($this->path(), 0, strrpos($this->path(), '/') + 1).
					$tgt['path'];
			
			$tgt['path'] = self::normalize($tgt['path']);
		}
		
		preg_match('~^'.
			'(?:(?<userinfo>'.self::RGX['userinfo'].')@)?'.
			'(?<host>'.self::RGX['host'].')'.
			'(?::(?<port>'.self::RGX['port'].'))?'.
			'$~iu', $tgt['authority'], $m
		);
		
		return new self(array_merge($tgt, $m));
	}
	
	/**
	 * Initializes the object's properties based on a given URI array data.
	 * @param array $data Data to be inserted into the object.
	 * @throws UriException A component in given data is invalid.
	 */
	protected function initialize(array $data): void
	{
		if(isset($data['query']) && !is_string($data['query']))
			$data['query'] = self::encode($data['query']);
		
		foreach(self::COMPONENT as $comp)
			if(isset($data[$comp]) && $data[$comp]) {
				if(!preg_match('~'.self::RGX[$comp].'~iu', $data[$comp]))
					throw UriException::unmatched($comp, $data[$comp]);
				
				$this->$comp = $data[$comp];
			}
	}
	
	/**
	 * Parses the URI passed as a string and builds the object's properties.
	 * @param string $uri URI to be parsed.
	 * @throws UriException The given string is not a valid URI.
	 */
	protected function parse(string $uri): void
	{
		static $regex;
		
		if(!isset($regex))
			$regex = '~^(?:(?<scheme>'.     self::RGX['scheme']  .'):)?+(?!$)'.
			    '(?://(?!$)(?:(?<userinfo>'.self::RGX['userinfo'].')@)?+(?!$)'.
			    '(?<host>'.                 self::RGX['host']    .')'.
			    '(?::(?<port>'.             self::RGX['port']    .'))?)?+'.
			    '(?<path>'.                 self::RGX['path']    .')?'.
			    '(?:\?(?<query>'.           self::RGX['query']   .'))?'.
			    '(?:#(?<fragment>'.         self::RGX['fragment'].'))?$~iu';
		
		if(!preg_match($regex, $uri, $m) && $uri)
			throw UriException::invalid($uri);
		
		foreach(self::COMPONENT as $comp)
			$this->$comp = isset($m[$comp]) && $m[$comp]
				? $m[$comp]
				: null;
	}
	
	/**
	 * Encodes a query component into a query string.
	 * @param array $data Data to be encoded as string.
	 * @return string The encoded query string.
	 */
	protected static function encode(array $data): string
	{
		$d = toarray($data);
		$d = http_build_query($d, null, '&', PHP_QUERY_RFC3986);
		return rtrim(str_replace(['=&','%5B','%5D'], ['&','[',']'], $d), '=');
	}
	
	/**
	 * Decodes a query string into an array.
	 * @param string $query The string to decode.
	 * @return array The query string as an array.
	 */
	protected static function decode(string $query): array
	{
		$q = str_replace('+', '%2B', $query);
		parse_str($q, $decoded);
		return $decoded;
	}
	
	/**
	 * Removes dot segments and normalizes the path.
	 * @param string $path Path to normalize.
	 * @return string The normalized path.
	 */
	protected static function normalize(?string $path): string
	{
		$norm = [null, null];
		$path = explode('/', $path);
		$actual = $leading = (int)(count($path) > 1 && !$path[0]);
		
		foreach($path as $segment)
			if($segment == '.') {
				$actual += (bool)$norm[$actual];
				$norm[$actual] = null;
			} elseif($segment == '..') {
				$actual -= $actual > $leading && !$norm[$actual];
				$norm[$actual] = null;
			} else {
				$actual += (bool)$norm[$actual];
				$norm[$actual] = $segment;
			}
		
		$path = implode('/', array_slice($norm, 0, $actual + 1));
		return $path;
	}
}
