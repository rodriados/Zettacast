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
 * Builds the graphs of objects that make up your application. The injector tracks
 * the dependencies for each type and uses bindings to inject them.
 * @since 1.0
 */
interface InjectorInterface
{
    /**
     * Injects dependencies directly into the fields and methods of the given instance.
     * Ignores the presence or absence of an injectable constructor.
     * @param object $instance The object instance to inject members on.
     * @see InjectorInterface::getFactory
     */
    public function injectMembers(object $instance): void;

    /**
     * Returns a type provider for the given type literal with a map of possible
     * named parameters to be overriden or used with specific values or instances.
     * @param string $type The type to get a provider for.
     * @param array $params The list of provider overridden parameters.
     * @return ProviderInterface A new type provider.
     */
    public function getProvider(string $type, array $params = []): ProviderInterface;

    /**
     * Returns the appropriate instance for the given injection type. When possible,
     * avoid using this method directly, in favor of having your dependencies injected
     * automatically by the framework ahead of time.
     * @param string $type The type to be instantiated.
     * @param array $params The list of parameters to override.
     * @return object The synthetized object instance.
     */
    public function getInstance(string $type, array $params = []): object;
}
