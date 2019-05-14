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

class SetterResolver extends ConstructorResolver implements ResolverInterface
{
    /**
     * Resolve the instance of the type.
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

        $callback = function ($parameter) use ($container, $parameters) {
            return $this->resolveParameter(
                $container,
                $parameter,
                $parameters
            );
        };

        foreach ($class->getMethods() as $method) {
            if (0 === strpos($method->name, 'set')) {
                $setterParameters = array_map(
                    $callback,
                    $method->getParameters()
                );

                try {
                    $method->invokeArgs($instance, $setterParameters);
                } catch (ReflectionException $e) {
                    throw new ContainerException($e->getMessage());
                }
            }
        }

        return $instance;
    }
}
