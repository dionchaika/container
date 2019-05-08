<?php

/**
 * The PSR-11 DI Container.
 *
 * @package dionchaika/container
 * @version 1.0.0
 * @license MIT
 * @author Dion Chaika <dionchaika@gmail.com>
 */

namespace Dionchaika\Container;

use ReflectionParameter;
use Psr\Container\ContainerInterface;

trait ResolverTrait
{
    /**
     * @param \Psr\Container\ContainerInterface $container
     * @param \ReflectionParameter              $parameter
     * @return mixed
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function resolveParameter(
        ContainerInterface $container,
        ReflectionParameter $parameter
    ) {
        $class = $parameter->getClass();
        if (null === $class) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }

            throw new ContainerException(
                'Parameter is not bound: '.$parameter->name.'!'
            );
        }

        return $container->get($class->name);
    }

    /**
     * @param \Psr\Container\ContainerInterface $container
     * @param string                            $property
     * @return mixed
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function resolveProperty(ContainerInterface $container, string $property)
    {
        return $container->get($property);
    }
}
