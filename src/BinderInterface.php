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
 * Collects configuration information, primarily bindings, which will be used to
 * create an injector. Ultimately, the binder shall be provided to your application's
 * module implementations so they each contribute their own bindings and configurations.
 * @since 1.0
 */
interface BinderInterface
{
    /**
     * Use the given module to configure more bindings.
     * @param ModuleInterface $module The module to be installed in this binder.
     */
    public function install(ModuleInterface $module): void;
}
