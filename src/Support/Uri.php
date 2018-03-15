<?php
/**
 * Zettacast\Support\Uri class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Support;

/**
 * The universal resource identification class. This class is responsible for
 * identifying external or internally known resources. This object simply holds
 * the URI and is not responsible for making sense out of it.
 * @property string $full The full URI, with all of its components.
 * @property string $scheme The URI scheme.
 * @property string $authority The URI authority.
 * @property string $userinfo The URI userinfo.
 * @property string $host The URI host.
 * @property int $port The URI host port.
 * @property string $path The URI path.
 * @property array $query The URI query.
 * @property string $fragment The URI fragment.
 * @package Zettacast\Support
 * @version 1.0
 */
class Uri
{
	/**
	 * URI components constant. This constant lists all existing components in
	 * an URI. Any other value besides these should and will be ignored when
	 * initializing an URI.
	 * @var array List of URI components.
	 */
	const COMPONENT = [
		'scheme', 'authority', 'userinfo', 'host', 'port',
		'path', 'query', 'fragment',
	];
	
	/**
	 * The split URI components, for individual usage.
	 * @var array URI components.
	 */
	protected $cp;
	
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
		
		$this->cp['query'] = array_merge($this->cp['query'], $query);
		$this->cp['full'] = $this->full();
	}
	
	/**
	 * Uri string representation magic method.
	 * Allows the object to be represented as a string.
	 * @return string String representation for this object.
	 */
	public function __tostring(): string
	{
		return $this->cp['full'];
	}
	
	/**
	 * Uri access property magic method.
	 * @param string $name The name of property to access.
	 * @return mixed The property value.
	 */
	public function __get(string $name)
	{
		return $this->cp[$name] ?? null;
	}
	
	/**
	 * Checks whether the stored URI is absolute.
	 * @return bool Is the URI absolute?
	 */
	public function isabsolute(): bool
	{
		return $this->cp['scheme']
		    || !$this->cp['authority'] && $this->cp['full'][0] == '/';
	}
	
	/**
	 * Checks whether the stored URI is relative.
	 * @return bool Is the URI relative?
	 */
	public function isrelative(): bool
	{
		return !$this->isabsolute();
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
		if(!$ref instanceof Uri)
			$ref = new Uri($ref);
		
		$tgt = [];
		$copyref = false;
		
		foreach(['scheme', 'authority', 'path', 'query', 'fragment'] as $d) {
			$copyref = $copyref || $ref->cp[$d];
			$tgt[$d] = $copyref ? $ref->cp[$d] : $this->cp[$d];
		}
		
		if(!$ref->cp['scheme'] && !$ref->cp['authority'] && $ref->cp['path']) {
			if($tgt['path'][0] != '/')
				$tgt['path'] = substr(
					$this->cp['path'], 0, strrpos($this->cp['path'], '/') + 1
				).$tgt['path'];
			
			$tgt['path'] = self::normalize($tgt['path']);
		}

		return new self($tgt);
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
		$segments = explode('/', $this->cp['path']);
		return $segments[$index] ?? null;
	}
	
	/**
	 * Rebuilds the full URI.
	 * @return string The full URI with all of its known components.
	 */
	protected function full(): string
	{
		/**
		 * @var string $scheme
		 * @var string $authority
		 * @var string $path
		 * @var array $query
		 * @var string $fragment
		 */
		extract($this->cp);
		
		foreach($query as $key => $value)
			$httpquery[] = $value != '' ? $key.'='.$value : $key;
		
		$full[] = $scheme ? $scheme.':' : null;
		$full[] = $authority;
		$full[] = self::normalize($path);
		$full[] = isset($httpquery) ? '?'.implode('&', $httpquery) : null;
		$full[] = $fragment ? '#'.$fragment : null;
		
		return implode('', $full);
	}
	
	/**
	 * Initializes the object's properties based on a given URI array data.
	 * @param array $data Data to be inserted into the object.
	 */
	protected function initialize(array $data): void
	{
		$d = [];
		
		foreach(self::COMPONENT as $part)
			$d[$part] = isset($data[$part]) && $data[$part]
				? $data[$part]
				: null;
		
		$this->cp = $d;
		$this->cp['port'] = $d['port'] ? (int)$d['port'] : null;
		parse_str($d['query'], $this->cp['query']);
	}
	
	/**
	 * Parses the URI passed as a string and builds the object's properties.
	 * @param string $uri URI to be parsed.
	 * @throws UriException The given string is not a valid URI.
	 */
	protected function parse(string $uri): void
	{
		$regex = '%^'.
			'(?:(?<scheme>[a-z][a-z0-9-+.]*):)?+(?!$)'.
			'(?<authority>//(?!$)'.
				'(?:(?<userinfo>[^@]*)@)?+(?!$)'.
				'(?<host>\[[0-9a-fv:.]*\]|[^:/?#]*)'.
				'(?::(?<port>[0-9]*))?'.
			')?+'.
			'(?<path>/|/?[^/?#]+(?:/[^/?#]*)*)?'.
			'(?:\?(?<query>[^#]*))?'.
			'(?:#(?<fragment>.*))?'.
			'$%iu';
		
		if(!preg_match($regex, $uri, $match) && $uri)
			throw UriException::invalid($uri);
		
		$this->initialize($match);
	}
	
	/**
	 * Removes dot segments and normalizes the path.
	 * @param string $path Path to normalize.
	 * @return string The normalized path.
	 */
	protected static function normalize(?string $path): string
	{
		$path = explode('/', $path);
		$leading = (int)(count($path) > 1 && !$path[0]);
		$norm = [null, null];
		$actual = $leading;
		
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
