<?php
/**
 * Zettacast\Stream\StreamContext class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Stream;

use Zettacast\Contract\ExtractableInterface;

/**
 * This class handles interactions to a stream contexts. It also allows the
 * usage of the dot notation to access and edit context values.
 * @package Zettacast\Stream
 * @version 1.0
 */
class StreamContext implements ExtractableInterface
{
	/**
	 * The resource encapsulated by this object. It can be both a stream or a
	 * pure context resource.
	 * @var resource The context related resource.
	 */
	protected $context;
	
	/**
	 * Initializes the stream context and prepares it to be applied to stream.
	 * @param resource|array $context Context data or the stream itself.
	 */
	public function __construct($context = [])
	{
		$this->context = self::create($context);
	}
	
	/**
	 * Allows accessing the context option identified by the given key. This
	 * key can be expressed in dot notation.
	 * @param string $opt The context option name to be accessed.
	 * @return mixed The requested option value or null if not found.
	 */
	public function get(string $opt)
	{
		$data = stream_context_get_params($this->context);
		$count = count($opt = explode('.', $opt, 2));
		
		if($count > 1 && isset($data['options'][$opt[0]]))
			$data = $data['options'][$opt[0]];
		
		return $data[$opt[$count - 1]] ?? null;
	}
	
	/**
	 * Allows changing or creating the context option identified by the given
	 * key. This key can be expressed in dot notation.
	 * @param string $opt The context option name to be changed or created.
	 * @param mixed $value The value to be related to the specified option.
	 * @return bool Was the operation successful?
	 */
	public function set(string $opt, $value): bool
	{
		if(count($opt = explode('.', $opt, 2)) > 1)
			array_push($opt, $value);
		
		return count($opt) > 1
			? stream_context_set_option($this->context, ...$opt)
			: stream_context_set_params($this->context, [$opt[0] => $value]);
	}
	
	/**
	 * Gives access to the object's raw contents. That is, it exposes the
	 * internal context resource, so it can be used when needed.
	 * @return resource The pure context resource.
	 */
	public function raw()
	{
		return $this->context;
	}
	
	/**
	 * Creates a raw context resource from the given parameter. If given an
	 * already created context resource, it will be simply returned unchanged.
	 * @param array|resource $context Context configuration to be created.
	 * @return resource The raw context resource created.
	 */
	public static function create($context)
	{
		return !is_resource($context)
			? stream_context_create(null, self::normalize($context))
			: $context;
	}
	
	/**
	 * Allows accessing or editing the global context configuration. If no
	 * context is explicitly given when instantiating a stream, the global
	 * context configuration will be the one used.
	 * @param array $context The new values global configuration must have.
	 * @return static The global context configuration.
	 */
	final public static function global(array $context = [])
	{
		return new static(empty($context)
			? stream_context_get_default()
			: stream_context_set_default(self::normalize($context)['options'])
		);
	}
	
	/**
	 * Normalizes a context array, so it is formatted as required by all of
	 * internal PHP functions.
	 * @param array $context Context array to be normalized.
	 * @return array The normalized array.
	 */
	protected static function normalize(array $context): array
	{
		if(empty($context))
			return stream_context_get_params(
				stream_context_get_default()
			);

		foreach($context as $key => $value)
			if(count($dot = explode('.', $key, 2)) > 1)
				$params['options'][$dot[0]][$dot[1]] = $value;
			elseif(is_array($value))
				$params['options'][$key] = $value;
			else
				$params[$key] = $value;
		
		return $params ?? [];
	}
	
}
