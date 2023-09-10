<?php
/**
 * Zettacast: a simple, fast and lightweight PHP dependency injection.
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2015-present Rodrigo Siqueira
 * @package Zettacast
 * @license MIT License
 */
namespace Zettacast\Internal;

use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use Zettacast\ProviderInterface;
use Zettacast\ProviderException;
use Zettacast\ZettacastException;
use Zettacast\Internal\Providers\ConstantProvider;
use Zettacast\Internal\Providers\InstanceProvider;

/**
 * The object assembler is responsible for building instances from bindings and does
 * it by reflecting into the type and injecting the required type instances.
 * @since 1.0
 */
class ReflectionAssembler
{
    /**
     * Returns a provider for the specified type literal.
     * @param string $type The type to get a provider for.
     * @param array $params The list of provider overridden parameters.
     * @return ProviderInterface A new type provider.
     * @throws ProviderException The specified type is not valid.
     */
    public function getProvider(string $type, array $params = []): ProviderInterface
    {
        $typeReflection = $this->getTypeReflection($type);
        $constructorProviders = $this->buildConstructorParameterProviders($typeReflection, $params);
        return new InstanceProvider($typeReflection, $constructorProviders);
    }

    /**
     * Gets the reflection of the given type.
     * @param string $type The type to be reflected over.
     * @return ReflectionClass The type reflector instance.
     * @throws ProviderException The specified type is not valid.
     */
    private function getTypeReflection(string $type): ReflectionClass
    {
        try {
            return new ReflectionClass($type);
        } catch (ReflectionException $e) {
            throw ProviderException::targetIsInvalid($type, $e);
        }
    }

    /**
     * Builds the providers for the constructor parameters of a type.
     * @param ReflectionClass $typeReflection The reflection of the target type.
     * @param array $params The list of provider overridden parameters.
     * @return ProviderInterface[] The type parameters' providers.
     */
    private function buildConstructorParameterProviders(ReflectionClass $typeReflection, array $params): array
    {
        if (!$constructorReflection = $typeReflection->getConstructor())
            return [];

        return array_map(
            fn (ReflectionParameter $parameter) => $this->buildParameterProvider($parameter, $params)
          , $constructorReflection->getParameters()
        );
    }

    /**
     * Builds the provider for a method parameter.
     * @param ReflectionParameter $parameter The target method parameter.
     * @param array $params The list of provider overridden parameters.
     * @return ProviderInterface The method parameter provider.
     */
    private function buildParameterProvider(ReflectionParameter $parameter, array $params): ProviderInterface
    {
        if (array_key_exists($parameterName = $parameter->getName(), $params))
            return new ConstantProvider($params[$parameterName]);

        throw ZettacastException::notImplemented(__METHOD__);
    }
}
