<?php

/**
 * The PSR-11 DI Container.
 *
 * @package dionchaika/container
 * @version 1.0.0
 * @license MIT
 * @author Dion Chaika <dionchaika@gmail.com>
 */

namespace Dionchaika\Container\Resolvers;

use ReflectionParameter;
use ReflectionException;
use Psr\Container\ContainerInterface;
use Dionchaika\Container\ContainerException;
use Dionchaika\Container\ParameterCollection;

trait ResolverTrait
{
    /**
     * Resolve parameter.
     *
     * @param \Psr\Container\ContainerInterface              $container
     * @param \ReflectionParameter                           $parameter
     * @param \Dionchaika\Container\ParameterCollection|null $parameters
     * @return mixed
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function resolveParameter(
        ContainerInterface $container,
        ReflectionParameter $parameter,
        ?ParameterCollection $parameters = null
    ) {
        $class = $parameter->getClass();
        if (null === $class) {
            if (null !== $parameters) {
                if ($parameters->has($parameter->name)) {
                    return $parameters
                        ->get($parameter->name)
                        ->getValue($container);
                }
            }

            if ($parameter->isDefaultValueAvailable()) {
                try {
                    return $parameter->getDefaultValue();
                } catch (ReflectionException $e) {
                    throw new ContainerException($e->getMessage());
                }
            }

            throw new ContainerException(
                'Parameter is not bound: '.$parameter->name.'!'
            );
        }

        return $container->make($class->name);
    }
}
