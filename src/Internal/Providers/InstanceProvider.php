<?php
/**
 * Zettacast: a simple, fast and lightweight PHP dependency injection.
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2015-present Rodrigo Siqueira
 * @package Zettacast
 * @license MIT License
 */
namespace Zettacast\Internal\Providers;

use ReflectionClass;
use ReflectionException;
use Zettacast\ProviderInterface;
use Zettacast\ProviderException;

/**
 * The provider of instances for an object type.
 * @since 1.0
 */
readonly class InstanceProvider implements ProviderInterface
{
    /**
     * Builds a new instance provider.
     * @param ReflectionClass $typeReflection The reflection of type to be provided.
     * @param ProviderInterface[] $parameterProviders The provider for instance parameters.
     */
    public function __construct(
        private ReflectionClass $typeReflection
      , private array $parameterProviders = []
    ) {}

    /**
     * Provides an instance of the specified type.
     * @return object The provided object instance.
     * @throws ProviderException Invalid parameters for type instantiation.
     */
    public function get(): mixed
    {
        try {
            return $this->typeReflection->newInstanceArgs(
                array_map(
                    fn(ProviderInterface $provider) => $provider->get()
                    , $this->parameterProviders
                )
            );
        } catch (ReflectionException $e) {
            throw ProviderException::invalidParameters($this->typeReflection->getName(), $e);
        }
    }
}
