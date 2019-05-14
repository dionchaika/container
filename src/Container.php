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
use ReflectionMethod;
use ReflectionException;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Dionchaika\Container\Resolvers\ResolverTrait;
use Dionchaika\Container\Resolvers\ConstructorResolver;

class Container implements ContainerInterface
{
    use ResolverTrait;

    /**
     * Enable autoresolving.
     *
     * @var bool
     */
    public $enableAutoresolving = true;

    /**
     * The container default resolver.
     *
     * @var \Dionchaika\Container\ResolverInterface
     */
    protected $resolver;

    /**
     * The container factory collection.
     *
     * @var \Dionchaika\Container\FactoryCollection
     */
    protected $factories;

    /**
     * The array of container resolved instances.
     *
     * @var mixed[]
     */
    protected $instances = [];

    /**
     * The container global instance.
     *
     * @var \Dionchaika\Container\Container
     */
    protected static $instance;

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
                    .'\\Dionchaika\\Container\\ResolverInterface!'
                );
            }

            $this->resolver = $config['resolver'];
        } else {
            $this->resolver = new ConstructorResolver;
        }

        if (isset($config['autoresolving'])) {
            $this->enableAutoresolving = $config['autoresolving'];
        }

        $this->factories = new FactoryCollection(
            isset($config['factories']) ? $config['factories'] : []
        );
    }

    /**
     * Get the container global instance.
     *
     * @param mixed[] $config
     * @return \Dionchaika\Container\Container
     * @throws \InvalidArgumentException
     */
    public static function getInstance(array $config = []): Container
    {
        if (null === static::$instance) {
            static::$instance = new static($config);
        }

        return static::$instance;
    }

    /**
     * Get the container default resolver.
     *
     * @return \Dionchaika\Container\ResolverInterface
     */
    public function getResolver(): ResolverInterface
    {
        return $this->resolver;
    }

    /**
     * Get the container factory collection.
     *
     * @return \Dionchaika\Container\FactoryCollection
     */
    public function getFactories(): FactoryCollection
    {
        return $this->factories;
    }

    /**
     * Get the array of container resolved instances.
     *
     * @return mixed[]
     */
    public function getInstances(): array
    {
        return $this->instances;
    }

    /**
     * Bind a new type to the container.
     *
     * @param string                     $id
     * @param \Closure|string|mixed|null $type
     * @param bool                       $singleton
     * @param mixed[]                    $parameters
     * @return \Dionchaika\Container\FactoryInterface
     */
    public function bind(
        string $id,
        $type = null,
        bool $singleton = false,
        array $parameters = []
    ): FactoryInterface {
        unset($this->instances[$id]);

        $type = $type ?? $id;
        if (!($type instanceof Closure)) {
            $type = $this->getClosure($type);
            $singleton = is_string($type) ? $singleton : true;
        }

        $params = [];
        foreach ($parameters as $name => $value) {
            $params[] = new Parameter($name, $value);
        }

        return $this->factories->add(new Factory(
            $id,
            $type,
            $singleton,
            !empty($params) ? new ParameterCollection($params) : null
        ));
    }

    /**
     * Set a new instance to the container.
     *
     * @param string $id
     * @param mixed  $instance
     * @return void
     */
    public function instance(string $id, $instance): void
    {
        $this->factories->delete($id);
        $this->instances[$id] = $instance;
    }

    /**
     * Bind a new singleton type to the container.
     *
     * @param string                     $id
     * @param \Closure|string|mixed|null $type
     * @param mixed[]                    $parameters
     * @return \Dionchaika\Container\FactoryInterface
     */
    public function singleton(string $id, $type = null, array $parameters = []): FactoryInterface
    {
        return $this->bind($id, $type, true, $parameters);
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
     * Make the instance of the type.
     *
     * @param string  $id
     * @param mixed[] $parameters
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function make(string $id, array $parameters = [])
    {
        if (!$this->has($id)) {
            $this->bind($id, null, false, $parameters);
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

        return $this->make($id);
    }

    /**
     * Call a method resolving parameters.
     *
     * @param string|mixed $type
     * @param string       $method
     * @param mixed[]      $parameters
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function call($type, string $method, array $parameters = [])
    {
        if (is_string($type)) {
            $type = $this->make($type);
        }

        try {
            $method = new ReflectionMethod($type, $method);
        } catch (ReflectionException $e) {
            throw new ContainerException($e->getMessage());
        }

        $params = [];
        foreach ($parameters as $name => $value) {
            $params[] = new Parameter($name, $value);
        }

        $params = !empty($params) ? new ParameterCollection($params) : null;

        $callback = function ($parameter) use ($params) {
            return $this->resolveParameter(
                $this,
                $parameter,
                $params
            );
        };

        $methodParameters = array_map($callback, $method->getParameters());

        try {
            return $method->invokeArgs($type, $methodParameters);
        } catch (ReflectionException $e) {
            throw new ContainerException($e->getMessage());
        }
    }

    /**
     * Get the closure for the type.
     *
     * @param string|mixed $type
     * @return \Closure
     */
    protected function getClosure($type): Closure
    {
        if (!is_string($type)) {
            return function () use ($type) { return $type; };
        }

        return function ($container, $parameters) use ($type) {
            return $container
                ->getResolver()
                ->resolve($container, $type, $parameters);
        };
    }
}
