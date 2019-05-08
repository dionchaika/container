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
use Psr\Container\ContainerInterface;

class PropertyResolver implements ResolverInterface
{
    use ResolverTrait;

    /**
     * Resolve an instance of the type.
     *
     * @param \Psr\Container\ContainerInterface $container
     * @param string                            $type
     * @return mixed
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function resolve(ContainerInterface $container, string $type)
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

        $instance = new $type;

        foreach ($class->getProperties() as $property) {
            if (preg_match('/\@var +([\w\\]+)/', $property->getDocComment(), $matches)) {
                $property->setValue($instance, $this->resolveProperty($container, $matches[1]));
            }
        }

        return $instance;
    }
}
