<?php
/**
 * Zettacast: a simple, fast and lightweight PHP dependency injection.
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2015-present Rodrigo Siqueira
 * @package Zettacast
 * @license MIT License
 */
namespace Zettacast;

use Exception;

/**
 * The base exception type for all exceptions thrown by the framework.
 * @since 1.0
 */
abstract class ZettacastException extends Exception
{
    /**
     * Informs that a method has not yet been implemented.
     * @param string $methodname The name of method that is not implemented.
     * @return ZettacastException The created exception instance.
     */
    public /* temporary */ static function notImplemented(string $methodname)
    {
        return new class($methodname) extends ZettacastException {
            public function __construct(string $methodname) {
                parent::__construct(message: "The method has not yet been implemented: '$methodname'.");
            }
        };
    }
}
