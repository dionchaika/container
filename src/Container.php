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
use Dionchaika\Container\Interfaces\FactoryInterface;
use Dionchaika\Container\Interfaces\ResolverInterface;
use Dionchaika\Container\Resolvers\ConstructorResolver;

class Container implements ContainerInterface
{
    /**
     * The instance resolver.
     *
     * @var \Dionchaika\Container\Interfaces\ResolverInterface
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
     * Allowed container config options:
     *
     *      1.  resolver (
     *              \Dionchaika\Container\Interfaces\ResolverInterface,
     *              default: \Dionchaika\Container\Resolvers\ConstructorResolver
     *          ) - the default instance resolver.
     *      2.  factories (
     *              \Dionchaika\Container\Interfaces\FactoryInterface[],
     *              default: empty
     *          ) - the array of default instance factories.
     *
     * @param mixed[] $config
     * @throws \InvalidArgumentException
     */
    public function __construct(array $config = [])
    {
        if (isset($config['resolver'])) {
            if (!($config['resolver'] instanceof ResolverInterface)) {
                throw new InvalidArgumentException(
                    'Resolver must be an instance of "\\Dionchaika\\Container\\Interfaces\\ResolverInterface"!'
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
     * Get the instance resolver.
     *
     * @return \Dionchaika\Container\Interfaces\ResolverInterface
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
     * @return \Dionchaika\Container\Interfaces\FactoryInterface
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

            foreach ($parameters as $name => $value) {
                $this->factories
                    ->get($id)
                    ->bindParameter($name, $value);
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
