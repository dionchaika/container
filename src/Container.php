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

/**
 * The DI container class.
 *
 * @see https://www.php-fig.org/psr/psr-11/
 */
class Container implements ContainerInterface
{
    /**
     * The array
     * of container factories.
     *
     * @var \Closure[]
     */
    protected $factories = [];

    /**
     * The array
     * of container instances.
     *
     * @var mixed[]
     */
    protected $instances = [];

    /**
     * The container singletons map.
     *
     * @var mixed[]
     */
    protected $singletons = [];

    /**
     * The array
     * of container parameters.
     *
     * @var mixed[]
     */
    protected $parameters = [];

    /**
     * Set a new instance.
     *
     * @param string $id
     * @param mixed  $instance
     * @return self
     */
    public function setInstance(string $id, $instance): self
    {
        $this->instances[$id] = $instance;
        $this->singletons[$id] = true;

        return $this;
    }

    /**
     * Set a new parameter.
     *
     * @param string $id
     * @param mixed  $parameter
     * @return self
     */
    public function setParameter(string $id, $parameter): self
    {
        $this->parameters[$id] = $parameter;
        return $this;
    }

    /**
     * Set a new factory.
     *
     * @param string   $id
     * @param \Closure $factory
     * @param bool     $singleton
     * @return self
     */
    public function setFactory(string $id, Closure $factory, bool $singleton = false): self
    {
        unset($this->instances[$id]);

        $this->factories[$id] = $factory;
        $this->singletons[$id] = $singleton;

        return $this;
    }

    /**
     * Set a new interface.
     *
     * @param string $id
     * @param string $interface
     * @param bool   $singleton
     * @return self
     */
    public function setInterface(string $id, string $interface, bool $singleton = false): self
    {
        return $this->setFactory($id, $this->getFactoryForInterface($interface), $singleton);
    }

    /**
     * Check is the container entry exists.
     *
     * @param string $id
     * @return bool
     */
    public function has($id)
    {
        return isset($this->instances[$id]) || isset($this->factories[$id]);
    }

    /**
     * Get the container entry.
     *
     * @param string $id
     * @return mixed
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException(
                'Entry does not exists: '.$id.'!'
            );
        }

        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        $instance = ($this->factories[$id])($this);

        if ($this->singletons[$id]) {
            $this->instances[$id] = $instance;
        }

        return $instance;
    }

    /**
     * Check is the container entry singleton.
     *
     * @param string $id
     * @return bool
     */
    public function isSingleton(string $id): bool
    {
        return isset($this->singletons[$id]) ? $this->singletons[$id] : false;
    }

    /**
     * Check is the container parameter exists.
     *
     * @param string $id
     * @return bool
     */
    public function hasParameter(string $id): bool
    {
        return isset($this->parameters[$id]);
    }

    /**
     * Get the container parameter.
     *
     * @param string $id
     * @return mixed|null
     */
    public function getParameter(string $id)
    {
        return $this->hasParameter($id) ? $this->parameters[$id] : null;
    }

    /**
     * Get the container parameters.
     *
     * @return mixed[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Get a factory for the interface.
     *
     * @param string $interface
     * @return \Closure
     */
    protected function getFactoryForInterface(string $interface): Closure
    {
        return function ($container) use ($interface) {
            try {

                $class = new ReflectionClass($interface);

            } catch (ReflectionException $e) {

                throw new ContainerException($e->getMessage());

            }

            if (!$class->isInstantiable()) {
                throw new ContainerException(
                    'Class is not instantiable: '.$interface.'!'
                );
            }

            $constructor = $class->getConstructor();
            if (null === $constructor) {
                return new $interface;
            }

            $parameters = array_map([$container, 'resolveParameter'], $constructor->getParameters());

            return $interface(...$parameters);
        };
    }

    /**
     * Resolve a parameter.
     *
     * @param \ReflectionParameter $parameter
     * @return mixed
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    protected function resolveParameter(ReflectionParameter $parameter)
    {
        $class = $parameter->getClass();
        if (null === $class) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }

            if (isset($this->parameters[$parameter->name])) {
                return $this->parameters[$parameter->name];
            }

            throw new ContainerException(
                'Parameter is not set: '.$parameter->name.'!'
            );
        }

        return $this->get($class->name);
    }
}
