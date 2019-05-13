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
use Dionchaika\Container\ParameterCollection;

class PropertyResolver extends ConstructorResolver implements ResolverInterface
{
    /**
     * Resolve an instance of the type.
     *
     * @param \Psr\Container\ContainerInterface              $container
     * @param string                                         $type
     * @param \Dionchaika\Container\ParameterCollection|null $parameters
     * @return mixed
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function resolve(
        ContainerInterface $container,
        string $type,
        ?ParameterCollection $parameters = null
    ) {
        $instance = parent::resolve($container, $type, $parameters);

        try {
            $class = new ReflectionClass($instance);
        } catch (ReflectionException $e) {
            throw new ContainerException($e->getMessage());
        }

        foreach ($class->getProperties() as $property) {
            if (preg_match('/\@var +([\w\\\]+)/', $property->getDocComment(), $matches)) {
                $property->setAccessible(true);

                if (
                    null !== $parameters &&
                    $parameters->has($property->name)
                ) {
                    $property->setValue($instance,
                        $parameters
                            ->get($property->name)
                            ->getValue($container)
                    );
                } else {
                    $property->setValue(
                        $instance,
                        $container->isAutoresoveEnabled() ? $container->make($matches[1]) : $container->get($matches[1])
                    );
                }
            }
        }

        return $instance;
    }
}
