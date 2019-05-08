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

trait ResolverTrait
{
    /**
     * Resolve a parameter.
     *
     * @param \Psr\Container\ContainerInterface          $container
     * @param \ReflectionParameter                       $parameter
     * @param \Dionchaika\Container\ParameterInterface[] $boundParameters
     * @return mixed
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function resolveParameter(ContainerInterface $container, ReflectionParameter $parameter, $boundParameters = [])
    {
        $class = $parameter->getClass();
        if (null === $class) {
            if ($parameter->isDefaultValueAvailable()) {
                try {
                    return $parameter->getDefaultValue();
                } catch (ReflectionException $e) {
                    throw new ContainerException($e->getMessage());
                }
            }

            foreach ($boundParameters as $boundParameter) {
                if ($boundParameter->getName() === $parameter->name) {
                    return $boundParameter->getValue($container);
                }
            }

            throw new ContainerException(
                'Parameter is not bound: '.$parameter->name.'!'
            );
        }

        return $container->get($class->name);
    }
}