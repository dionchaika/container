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
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Dionchaika\Container\Resolvers\ConstructorResolver;

class Container implements ContainerInterface
{
    /**
     * The default resolver.
     *
     * @var \Dionchaika\Container\ResolverInterface
     */
    protected $resolver;

    /**
     * The factory collection.
     *
     * @var \Dionchaika\Container\FactoryCollection
     */
    protected $factories;

    /**
     * The array of resolved instances.
     *
     * @var mixed[]
     */
    protected $instances = [];

    /**
     * @param mixed[] $config
     * @throws \InvalidArgumentException
     */
    public function __construct(array $config = [])
    {
        if (isset($config['resolver'])) {
            if (!($config['resolver'] instanceof ResolverInterface)) {
                throw new InvalidArgumentException(
                    'Resolver must be an instance of '
                    .'"\\Dionchaika\\Container\\Interfaces\\ResolverInterface"!'
                );
            }

            $this->resolver = $config['resolver'];
        } else {
            $this->resolver = new ConstructorResolver;
        }

        $this->factories = new FactoryCollection(
            isset($config['factories']) ? $config['factories'] : []
        );
    }

    /**
     * Get the default resolver.
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
            $type = $this->getClosure($type);
            $singleton = is_string($type) ? $singleton : true;
        }

        return $this->factories->set(new Factory($id, $type, $singleton));
    }

    /**
     * Bind a new singleton type to the container.
     *
     * An alias method name to bind.
     *
     * @param string                     $id
     * @param \Closure|string|mixed|null $type
     * @return \Dionchaika\Container\Interfaces\FactoryInterface
     */
    public function singleton(string $id, $type = null): FactoryInterface
    {
        return $this->bind($id, $type, true);
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
     * Resolve the instance of the type.
     *
     * @param string  $id
     * @param mixed[] $parameters
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function resolve(string $id, array $parameters = [])
    {
        if (!$this->has($id)) {
            $this->bind($id);

            foreach ($parameters as $key => $value) {
                if (is_int($key)) {
                    $this->factories
                        ->get($id)
                        ->bindParameter($value);
                } else {
                    $this->factories
                        ->get($id)
                        ->bindNamedParameter($key, $value);
                }
            }
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

        return $this->resolve($id);
    }

    /**
     * Get the closure for the type.
     *
     * @param string|mixed $type
     * @return \Closure
     */
    protected function getClosure($type): Closure
    {
        return is_string($type)
            ? function ($container, $params) use ($type) {
                return $container
                    ->getResolver()
                    ->resolve($container, $type, $params);
            }
            : function () use ($type) { return $type; };
    }
}
