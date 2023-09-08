<?php
/**
 * Zettacast: a simple, fast and lightweight PHP dependency injection.
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2015-present Rodrigo Siqueira
 * @package Zettacast
 * @license MIT License
 */
namespace Zettacast;

use Closure;

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
     * Returns this injector's named bindings. These bindings are either created
     * manually by the user or discovered automatically the framework. It is not
     * guaranteed that all bindings in the whole application will ever be present
     * in the returned value, but only those bound explicitly by a module.
     * @return array The injector's bindings.
     */
    public function getBindings(): array;

    /**
     * Returns a type factory for the given type literal with a map of possible named
     * parameters to be overriden or used with scalar values.
     * @param string $type The type to get a factory for.
     * @param array $params The list of factory overridden parameters.
     * @return Closure A new type factory.
     */
    public function getFactory(string $type, array $params = []): Closure;

    /**
     * Returns the appropriate instance for the given injection type. When possible,
     * avoid using this method directly, in favor of having your dependencies injected
     * automatically by the framework ahead of time.
     * @param string $type The type to be instantiated.
     * @return mixed The synthetized instance.
     */
    public function getInstance(string $type): mixed;

    /**
     * Returns the injector's parent, if any is known.
     * @return InjectorInterface|null The parent injector if any.
     */
    public function getParent(): ?InjectorInterface;

    /**
     * Returns a new injector that inherits all state from this injector. All bindings,
     * scopes and interceptors are inherited and visible to the child injector.
     * @param ModuleInterface ...$modules The modules to start child injector with.
     * @return InjectorInterface The new child injector instance.
     */
    public function createChildInjector(ModuleInterface... $modules): InjectorInterface;
}
