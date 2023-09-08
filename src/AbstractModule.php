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
 * A helper class used to add bindings to an injector.
 * @since 1.0
 */
abstract class AbstractModule implements ModuleInterface
{
    /**
     * The binder instance to which this module must configure.
     * @var BinderInterface|null The module's target binder instance.
     */
    private ?BinderInterface $binder = null;

    /**
     * Contributes bindings and other configurations for this module.
     * @param BinderInterface $binder The binder to configure the module for.
     */
    public final function useBinder(BinderInterface $binder): void
    {
        if (!is_null($this->binder)) {
            throw ModuleException::reentryIsNotAllowed();
        }

        try {
            $this->binder = $binder;
            $this->configure();
        } finally {
            $this->binder = null;
        }
    }

    /**
     * Configures a binder via the exposed methods.
     * @see AbstractModule::bind
     */
    protected abstract function configure(): void;
}
