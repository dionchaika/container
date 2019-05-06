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
     * The container
     * singletons map.
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
     * The array
     * of container bound parameters.
     *
     * @var mixed[]
     */
    protected $boundParameters = [];

    /**
     * Set a new parameter.
     *
     * @param string $name
     * @param mixed  $value
     * @return self
     */
    public function setParameter(string $name, $value): self
    {
        $this->parameters[$name] = $value;
        return $this;
    }

    /**
     * Check is the parameter exists.
     *
     * @param string $name
     * @return bool
     */
    public function hasParameter(string $name): bool
    {
        return isset($this->parameters[$name]);
    }

    /**
     * Get the parameter.
     *
     * @param string $name
     * @return mixed|null
     */
    public function getParameter(string $name)
    {
        return $this->hasParameter($name) ? $this->parameters[$name] : null;
    }

    /**
     * Get parameters.
     *
     * @return mixed[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Bind parameters.
     *
     * @param string  $id
     * @param mixed[] $parameters
     * @return self
     */
    public function bindParameters($id, array $parameters): self
    {
        $this->boundParameters[$id] = $parameters;
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
    public function setFactory($id, Closure $factory, bool $singleton = false): self
    {
        unset($this->instances[$id]);

        $this->factories[$id] = $factory;
        $this->singletons[$id] = $singleton;

        return $this;
    }

    /**
     * Set a new instance.
     *
     * @param string $id
     * @param mixed  $instance
     * @return self
     */
    public function setInstance($id, $instance): self
    {
        $this->instances[$id] = $instance;
        $this->singletons[$id] = true;

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
    public function setInterface($id, string $interface, bool $singleton = false): self
    {
        return $this->setFactory($id, $this->getFactoryForInterface($id, $interface), $singleton);
    }

    /**
     * Check is the entry singleton.
     *
     * @param string $id
     * @return bool
     */
    public function isSingleton($id): bool
    {
        return isset($this->singletons[$id]) ? $this->singletons[$id] : false;
    }

    /**
     * Check is the entry exists.
     *
     * @param string $id
     * @return bool
     */
    public function has($id)
    {
        return isset($this->factories[$id]) || isset($this->instances[$id]);
    }

    /**
     * Get the entry.
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
     * Get a factory for the interface.
     *
     * @param string $id
     * @param string $interface
     * @return \Closure
     */
    protected function getFactoryForInterface($id, string $interface): Closure
    {
        return function ($container) use ($id, $interface) {
            try {

                $class = new ReflectionClass($interface);

            } catch (ReflectionException $e) {

                throw new ContainerException($e->getMessage());

            }

            if (!$class->isInstantiable()) {
                throw new ContainerException(
                    'Entry is not instantiable: '.$id.'!'
                );
            }

            $constructor = $class->getConstructor();
            if (null === $constructor) {
                return new $interface;
            }

            $parameters = array_map(function ($parameter) use ($id, $container) {
                return $container->resolveParameter($id, $parameter);
            }, $constructor->getParameters());

            return new $interface(...$parameters);
        };
    }

    /**
     * Resolve a parameter.
     *
     * @param \ReflectionParameter $parameter
     * @param string $id
     * @return mixed
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    protected function resolveParameter($id, ReflectionParameter $parameter)
    {
        $class = $parameter->getClass();
        if (null === $class) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }

            if (isset($this->boundParameters[$id][$parameter->name])) {
                return $this->boundParameters[$id][$parameter->name];
            }

            throw new ContainerException(
                'Parameter of entry "'.$id.'" is not bound: '.$parameter->name.'!'
            );
        }

        return $this->get($class->name);
    }
}
