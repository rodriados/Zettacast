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
 * The entry point to the Zettacast framework.
 * @since 1.0
 */
final class Zettacast
{
    /**
     * Disallows the instantiation of the framework entry point class.
     * @see Zettacast::createInjector
     */
    private function __construct() {}

    /**
     * Creates a new injector from the given set of modules.
     * @param ModuleInterface ...$modules The modules to start injector with.
     * @return InjectorInterface The new injector instance.
     */
    public static function createInjector(ModuleInterface... $modules): InjectorInterface
    {
        throw new Exception("function not yet implemented!");
    }
}
