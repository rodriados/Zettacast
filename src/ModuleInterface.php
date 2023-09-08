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
 * A module contributes configuration information, typically interface bindings,
 * which will be used to create an injector.
 * @since 1.0
 */
interface ModuleInterface
{
    /**
     * Contributes bindings and other configurations for this module.
     * @param BinderInterface $binder The binder to configure the module for.
     */
    public function useBinder(BinderInterface $binder): void;
}
