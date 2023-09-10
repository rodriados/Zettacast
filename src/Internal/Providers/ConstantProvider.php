<?php
/**
 * Zettacast: a simple, fast and lightweight PHP dependency injection.
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2015-present Rodrigo Siqueira
 * @package Zettacast
 * @license MIT License
 */
namespace Zettacast\Internal\Providers;

use Zettacast\ProviderInterface;

/**
 * The provider for a constant value.
 * @since 1.0
 */
readonly class ConstantProvider implements ProviderInterface
{
    /**
     * Builds a new value provider.
     * @param mixed $constant The value to be provided.
     */
    public function __construct(
        private mixed $constant
    ) {}

    /**
     * Provides an instance of the specified type.
     * @return object The provided object instance.
     */
    public function get(): mixed
    {
        return $this->constant;
    }
}
