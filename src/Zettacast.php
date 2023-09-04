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
 * @version 1.0
 */
final class Zettacast
{
    /**
     * Disallows the instantiation of the framework entry point.
     * @see Zettacast::createInjector
     */
    private function __construct() {}

    /**
     * Creates a new injector from the given set of modules.
     * @return never The function is not yet implemented.
     * @throws Exception The function is not yet implemented.
     */
    public static function createInjector(): never
    {
        throw new Exception("function not yet implemented!");
    }
}
