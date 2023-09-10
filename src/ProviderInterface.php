<?php
/**
 * Zettacast: a simple, fast and lightweight PHP dependency injection.
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2015-present Rodrigo Siqueira
 * @package Zettacast
 * @license MIT License
 */
namespace Zettacast;

/**
 * An object capable of providing instances of a specific type.
 * @since 1.0
 */
interface ProviderInterface
{
    /**
     * Provides an instance of the specified type.
     * @return object The provided object instance.
     */
    public function get(): mixed;
}
