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
use Psr\Container\ContainerInterface;
use Dionchaika\Container\Interfaces\FactoryInterface;
use Dionchaika\Container\Resolvers\ConstructorResolver;

class Container implements ContainerInterface
{
    /**
     * The instance resolver.
     *
     * @var \Dionchaika\Container\ResolverInterface
     */
    protected $resolver;

    /**
     * The instance factories.
     *
     * @var \Dionchaika\Container\FactoryCollection
     */
    protected $factories;

    /**
     * The array
     * of resolved instances.
     *
     * @var mixed[]
     */
    protected $instances = [];

    /**
     * @param mixed[] $config
     */
    public function __construct(array $config = [])
    {
        if (
            isset($config['resolver']) &&
            $config['resolver'] instanceof ResolverInterface
        ) {
            $this->resolver = $config['resolver'];
        } else {
            $this->resolver = new ConstructorResolver;
        }

        $this->factories = new FactoryCollection(
            isset($config['factories']) ? $config['factories'] : []
        );
    }

    /**
     * Get the instance resolver.
     *
     * @return \Dionchaika\Container\ResolverInterface
     */
    public function getResolver(): ResolverInterface
    {
        return $this->resolver;
    }

    /**
     * Bind a new type to the container.
     *
     * @param string                     $id
     * @param \Closure|string|mixed|null $type
     * @param bool                       $singleton
     * @return \Dionchaika\Container\FactoryInterface
     */
    public function bind(string $id, $type = null, bool $singleton = false): FactoryInterface
    {
        unset($this->instances[$id]);

        $type = $type ?? $id;

        if (!($type instanceof Closure)) {
            if (is_string($type)) {
                $type = $this->getClosureForType($type);
            } else {
                $type = $this->getClosureForInstance($type);
                $singleton = true;
            }
        }

        return $this->factories->set(new Factory($id, $type, $singleton));
    }

    /**
     * Check is the type
     * exists in the container.
     *
     * @param string $id
     * @return bool
     */
    public function has($id)
    {
        return isset($this->instances[$id]) || $this->factories->has($id);
    }

    /**
     * Get the instance of the type.
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
                'Type is not exists in the container: '.$id.'!'
            );
        }

        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        $instance = $this->factories
            ->get($id)
            ->getInstance($this);

        if (
            $this->factories
                ->get($id)
                ->isSingleton()
        ) {
            $this->instances[$id] = $instance;
        }

        return $instance;
    }

    /**
     * Get the closure for the type.
     *
     * @param string $type
     * @return \Closure
     */
    protected function getClosureForType($type): Closure
    {
        return function ($container, $parameters) use ($type) {
            return $container
                ->getResolver()
                ->resolve($container, $type, $parameters);
        };
    }

    /**
     * Get the closure for the instance.
     *
     * @param mixed $instance
     * @return \Closure
     */
    protected function getClosureForInstance($instance): Closure
    {
        return function () use ($instance) { return $instance; };
    }
}
