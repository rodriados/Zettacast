<?php
/**
 * Zettacast\Injector\Exception\Unresolvable exception file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2017 Rodrigo Siqueira
 */
namespace Zettacast\Injector\Exception;

use Exception;
use ReflectionParameter;

class NotResolvableException
	extends Exception
{
	public function __construct(
		ReflectionParameter $param,
		Exception $previous = null
	) {
		parent::__construct(sprintf(
			'Unresolvable %s in %s::%s',
			$param->name,
			$param->getDeclaringClass()->name,
			$param->getDeclaringFunction()->name
		), $previous->code, $previous);
	}
}
