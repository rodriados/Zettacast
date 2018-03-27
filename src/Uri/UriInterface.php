<?php
/**
 * Zettacast\Uri\UriInterface interface file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Uri;

interface UriInterface
{
	/**
	 * Uri string representation magic method.
	 * Allows the object to be represented as a string.
	 * @return string String representation for this object.
	 */
	public function __tostring(): string;
	
	/**
	 * Retrieves the URI scheme.
	 * @return string The URI scheme.
	 */
	public function scheme(): ?string;
	
	/**
	 * Retrieves the URI user information.
	 * @return string The URI user information.
	 */
	public function userinfo(): ?string;
	
	/**
	 * Retrieves the URI host.
	 * @return string The URI host.
	 */
	public function host(): ?string;
	
	/**
	 * Retrieves the URI port.
	 * @return int The URI port.
	 */
	public function port(): ?int;
	
	/**
	 * Retrieves the URI authority.
	 * @return string The URI authority.
	 */
	public function authority(): ?string;
	
	/**
	 * Retrieves the URI path.
	 * @return string The URI path.
	 */
	public function path(): ?string;
	
	/**
	 * Retrieves the URI query.
	 * @return string[] The URI query.
	 */
	public function query(): ?array;
	
	/**
	 * Retrieves the URI fragment.
	 * @return string The URI fragment.
	 */
	public function fragment(): ?string;
	
	/**
	 * Rebuilds the full URI.
	 * @return string The full URI with all of its known components.
	 */
	public function full(): string;

	/**
	 * Transforms given URI as a reference using the instanciated object as a
	 * base for the transformation. This method executes reference
	 * transformation in conformity with RFC3986.
	 * @param string|array|Uri $ref The reference to be transformed.
	 * @return UriInterface The transformed reference.
	 */
	public function reference($ref): UriInterface;
}
