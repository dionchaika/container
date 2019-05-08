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
use Dionchaika\Container\ContainerException;
use Dionchaika\Container\ParameterCollection;
use Dionchaika\Container\Interfaces\ResolverInterface;

class PropertyResolver extends ConstructorResolver implements ResolverInterface
{
    /**
     * Resolve an instance of the type.
     *
     * @param \Psr\Container\ContainerInterface              $container
     * @param string                                         $type
     * @param \Dionchaika\Container\ParameterCollection|null $boundParameters
     * @return mixed
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function resolve(
        ContainerInterface $container,
        string $type,
        ?ParameterCollection $boundParameters = null
    ) {
        $instance = parent::resolve($container, $type, $boundParameters);

        try {
            $class = new ReflectionClass($instance);
        } catch (ReflectionException $e) {
            throw new ContainerException($e->getMessage());
        }

        foreach ($class->getProperties() as $property) {
            if (preg_match('/\@var +([\w\\\]+)/', $property->getDocComment(), $matches)) {
                $property->setAccessible(true);

                if ($container->has($matches[1])) {
                    $property->setValue($instance, $container->get($matches[1]));
                } else if ($boundParameters->has($property->name)) {
                    $property->setValue($instance, $boundParameters->get($property->name));
                } else {
                    throw new ContainerException(
                        'Property is not bound: '.$property->name.'!'
                    );
                }
            }
        }

        return $instance;
    }
}
