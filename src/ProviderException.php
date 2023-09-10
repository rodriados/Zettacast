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
 * The exception type for all errors related to providers.
 * @since 1.0
 */
class ProviderException extends ZettacastException
{
    /**
     * Returns an exception for when a provider for an invalid type is requested.
     * @param Throwable|null $previous The parent exception instance.
     * @return static The new provider exception instance.
     */
    public static function targetIsInvalid(string $target, ?Throwable $previous = null): static
    {
        return new static(
            message: "No provider can be created for unknown class or interface: '$target'."
          , previous: $previous
        );
    }

    /**
     * Returns an exception for invalid parameters when instantiating a type.
     * @param string $type The type that failed to be instantiated.
     * @param Throwable|null $previous The parent exception instance.
     * @return static The new provider exception instance.
     */
    public static function invalidParameters(string $type, ?Throwable $previous = null): static
    {
        return new static(
            message: "Invalid parameters when instantiating type: '$type'."
          , previous: $previous
        );
    }
}
