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

use ReflectionClass;
use ReflectionException;

class SetterResolver
{
    /**
     * @param string $interface
     * @return mixed
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function resolve(string $interface)
    {
        try {
            $class = new ReflectionClass($interface);
        } catch (ReflectionException $e) {
            throw new ContainerException($e->getMessage());
        }

        if (!$class->isInstantiable()) {
            throw new ContainerException(
                'Entry is not instantiable: '.$interface.'!'
            );
        }

        $instance = new $interface;

        foreach ($class->getMethods() as $method) {
            if (0 === strpos($method->name, 'set')) {
                $parameters = array_map(
                    ['static', 'resolveParameter'],
                    $method->getParameters()
                );

                $instance->{$method->name}(...$parameters);
            }
        }

        return $interface;
    }
}
