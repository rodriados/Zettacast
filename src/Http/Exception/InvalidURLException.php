<?php
/**
 * Zettacast\HTTP\Exception\StreamDoesNotExist exception file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Http\Exception;

class InvalidURLException
	extends \Exception
{
	public function __construct(string $url)
	{
		parent::__construct($url.' is an invalid URL.');
	}
}
