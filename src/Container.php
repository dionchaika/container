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
use Dionchaika\Container\Resolvers\ConstructorResolver;

/**
 * <code>
 *      $container = new Container;
 *
 *      $container->bind('db', \PDO::class)
 *          ->asSingleton()
 *          ->bindParameter('passwd', $passwd)
 *          ->bindParameter('username', $username)
 *          ->bindParameter('dsn', function ($container) {
 *              $host = $container->getParameter('db.host');
 *              $dbname = $container->getParameter('db.name');
 *              $charset = $container->getParameter('db.charset');
 *
 *              return "mysql:host={$host};dbname={$dbname};charset={$charset}";
 *          });
 *
 *      if ($container->has('db')) {
 *          $db = $container->get('db');
 *      }
 * </code>
 */
class Container implements ContainerInterface
{
    /**
     * The container resolver.
     *
     * @var \Dionchaika\Container\ResolverInterface
     */
    protected $resolver;

    /**
     * The container factories.
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
     * @param \Dionchaika\Container\ResolverInterface|null $resolver
     */
    public function __construct(?ResolverInterface $resolver = null)
    {
        $this->resolver = $resolver ?? new ConstructorResolver;
    }

    /**
     * Get the container resolver.
     *
     * @return \Dionchaika\Container\ResolverInterface
     */
    public function getResolver(): ResolverInterface
    {
        return $this->resolver;
    }

    /**
     * Get the container factories.
     *
     * @return \Dionchaika\Container\FactoryCollection
     */
    public function getFactories(): FactoryCollection
    {
        return $this->factories;
    }

    /**
     * Get the array
     * of resolved instances.
     *
     * @return mixed[]
     */
    public function getInstances(): array
    {
        return $this->instances;
    }

    /**
     * Bind a new type.
     *
     * @param string                     $id
     * @param \Closure|string|mixed|null $type
     * @param bool                       $singleton
     * @return \Dionchaika\Container\Factory
     */
    public function bind($id, $type = null, bool $singleton = false): Factory
    {
        unset($this->instances[$id]);

        $type = $type ?? $id;

        if (!($type instanceof Closure)) {
            $type = is_string($type)
                ? $this->getClosureForType($type)
                : $this->getClosureForInstance($type);

            $singleton = is_string($type) ? $singleton : true;
        }

        $this->factories->set($id, new Factory($id, $type, $singleton));
        return $this->factories->get($id);
    }

    /**
     * Check is the type exists.
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
                'Type is not found: '.$id.'!'
            );
        }

        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        $instance = $this->factories->get($id)->getInstance($this);
        if ($this->factories->get($id)->isSingleton()) {
            $this->instances[$id] = $instance;
        }

        return $instance;
    }

    /**
     * Get the closure for type.
     *
     * @param string $type
     * @return \Closure
     */
    protected function getClosureForType($type): Closure
    {
        return function ($container, $parameters) use ($type) {
            return $container->getResolver()->resolve(
                $container,
                $type,
                $parameters
            );
        };
    }

    /**
     * Get the closure for an instance.
     *
     * @param mixed $instance
     * @return \Closure
     */
    protected function getClosureForInstance($instance): Closure
    {
        return function () use ($instance) {
            return $instance;
        };
    }
}
