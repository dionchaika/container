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

        $callback = function ($parameter) use ($container, $boundParameters) {
            return $this->resolveParameter(
                $container,
                $parameter,
                $boundParameters
            );
        };

        foreach ($class->getMethods() as $method) {
            if (0 === strpos($method->name, 'set')) {
                $parameters = array_map($callback, $method->getParameters());

                try {
                    $method->invokeArgs($instance, $parameters);
                } catch (ReflectionException $e) {
                    throw new ContainerException($e->getMessage());
                }
            }
        }

        return $instance;
    }
}
