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

use ReflectionClass;
use ReflectionException;
use Psr\Container\ContainerInterface;
use Dionchaika\Container\ResolverInterface;
use Dionchaika\Container\ContainerException;

class ConstructorResolver implements ResolverInterface
{
    use ResolverTrait;

    /**
     * Resolve an instance of the type.
     *
     * @param \Psr\Container\ContainerInterface          $container
     * @param string                                     $type
     * @param \Dionchaika\Container\ParameterInterface[] $parameters
     * @return mixed
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function resolve(ContainerInterface $container, string $type, array $parameters = [])
    {
        try {
            $class = new ReflectionClass($type);
        } catch (ReflectionException $e) {
            throw new ContainerException($e->getMessage());
        }

        if (!$class->isInstantiable()) {
            throw new ContainerException(
                'Type is not instantiable: '.$type.'!'
            );
        }

        $constructor = $class->getConstructor();
        if (null === $constructor) {
            try {
                return $class->newInstanceWithoutConstructor();
            } catch (ReflectionException $e) {
                throw new ContainerException($e->getMessage());
            }
        }

        $callback = function ($parameter) use ($container, $parameters) {
            return $this->resolveParameter(
                $container,
                $parameter,
                $parameters
            );
        };

        $parameters = array_map($callback, $constructor->getParameters());

        try {
            return $class->newInstanceArgs($parameters);
        } catch (ReflectionException $e) {
            throw new ContainerException($e->getMessage());
        }
    }
}
