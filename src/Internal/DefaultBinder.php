<?php
/**
 * Zettacast: a simple, fast and lightweight PHP dependency injection.
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2015-present Rodrigo Siqueira
 * @package Zettacast
 * @license MIT License
 */
namespace Zettacast\Internal;

use Zettacast\BinderInterface;
use Zettacast\ModuleInterface;

/**
 * The default binder type used internally by Zettacast.
 * @since 1.0
 */
class DefaultBinder implements BinderInterface
{
    /**
     * Use the given module to configure more bindings.
     * @param ModuleInterface $module The module to be installed in this binder.
     */
    public function install(ModuleInterface $module): void
    {
        $module->useBinder($this);
        //$this->discoverBindingsFromMethods($module);
    }

    /**
     * Builds a new object assembler from the bindings registered in this binder.
     * @return ReflectionAssembler The new object assembler instance.
     */
    public function buildObjectAssembler(): ReflectionAssembler
    {
        return new ReflectionAssembler();
    }

    /**
     * Creates a new binder by bootstrapping it with new modules.
     * @param ModuleInterface[] $modules The modules to create a new binder with.
     * @return static The new binder instance.
     */
    public static function fromModules(ModuleInterface... $modules): static
    {
        $binder = new static();
        array_walk($modules, [$binder, 'install']);
        return $binder;
    }
}
