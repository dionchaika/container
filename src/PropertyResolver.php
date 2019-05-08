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

class PropertyResolver
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

        foreach ($class->getProperties() as $property) {
            if (preg_match('/\@var +([\w\\]+)/', $property->getDocComment(), $matches)) {
                $class = $matches[1];

                if ($this->container->has($class)) {
                    $property->setValue($instance, $this->container->get($class));
                }
            }
        }

        return $interface;
    }
}
