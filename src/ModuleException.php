<?php
/**
 * Zettacast: a simple, fast and lightweight PHP dependency injection.
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2015-present Rodrigo Siqueira
 * @package Zettacast
 * @license MIT License
 */
namespace Zettacast;

use Throwable;

/**
 * The exception type for all errors related to modules
 * @since 1.0
 */
class ModuleException extends ZettacastException
{
    /**
     * Returns an exception for when a module re-entry happens.
     * @param Throwable|null $previous The parent exception instance.
     * @return ModuleException The new module exception instance.
     */
    public static function reentryIsNotAllowed(?Throwable $previous = null): ModuleException
    {
        return new ModuleException(
            message: "Re-entry to modules is not allowed."
          , previous: $previous
        );
    }
}
