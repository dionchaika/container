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

use Closure;
use ReflectionClass;
use ReflectionParameter;
use ReflectionException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /**
     * @var \Dionchaika\Container\FactoryCollection
     */
    protected $factories;

    /**
     * @var mixed[]
     */
    protected $instances = [];

    /**
     * @var mixed[]
     */
    protected $singletons = [];

    /**
     * @param string $id
     * @param mixed  $instance
     * @return void
     */
    public function bindInstance($id, $instance): void
    {
        $this->instances[$id] = $instance;
        $this->singletons[$id] = true;
    }

    /**
     * @param string   $id
     * @param \Closure $closure
     * @param bool     $singleton
     * @return \Dionchaika\Container\FactoryInterface
     */
    public function bindFactory($id, Closure $closure, bool $singleton = false): FactoryInterface
    {
        unset($this->instances[$id]);

        $this->singletons[$id] = $singleton;
        return $this->factories->set($id, new Factory($closure));
    }

    /**
     * @param string $id
     * @param string $interface
     * @param bool   $singleton
     * @return \Dionchaika\Container\FactoryInterface
     */
    public function bindInterface($id, string $interface, bool $singleton = false): FactoryInterface
    {
        return $this->bindFactory($id, $this->getClosureForInterface($id, $interface), $singleton);
    }

    /**
     * @param string $id
     * @param string $interface
     * @return \Closure
     */
    protected function getClosureForInterface($id, string $interface): Closure
    {
        return function ($container) use ($id, $interface) {
            try {
                $class = new ReflectionClass($interface);
            } catch (ReflectionException $e) {
                throw new ContainerException(
                    $e->getMessage(), $e->getCode(), $e->getPrevious()
                );
            }

            if (!$class->isInstantiable()) {
                throw new ContainerException(
                    'Entry is not instantiable: '.$interface.'!'
                );
            }

            $constructor = $class->getConstructor();
            if (null === $constructor) {
                return new $interface;
            }

            $parameters = array_map(function ($parameter) use ($id, $container) {
                return $container->getParameter($id, $parameter);
            }, $constructor->getParameters());

            return new $interface(...$parameters);
        };
    }

    /**
     * @param string               $id
     * @param \ReflectionParameter $parameter
     * @return mixed
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    protected function getParameter($id, ReflectionParameter $parameter)
    {
        $class = $parameter->getClass();
        if (null === $class) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }

            
        }
    }
}
