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

class Factory implements FactoryInterface
{
    /**
     * The factory name.
     *
     * @var string
     */
    protected $name;

    /**
     * The factory closure.
     *
     * @var \Closure
     */
    protected $closure;

    /**
     * Is the resolved
     * instance should be
     * managed as a singleton.
     *
     * @var bool
     */
    protected $singleton = false;

    /**
     * The factory parameters.
     *
     * @var \Dionchaika\Container\ParameterCollection
     */
    protected $parameters;

    /**
     * @param string   $name
     * @param \Closure $closure
     * @param bool     $singleton
     */
    public function __construct(string $name, Closure $closure, bool $singleton = false)
    {
        $this->name = $name;
        $this->closure = $closure;
        $this->singleton = $singleton;

        $this->parameters = new ParameterCollection;
    }

    /**
     * Get the factory name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Manage the resolved
     * instance as a singleton.
     *
     * @return self
     */
    public function asSingleton(): self
    {
        $this->singleton = true;
        return $this;
    }

    /**
     * Check is the resolved
     * instance should be managed as a singleton.
     *
     * @return bool
     */
    public function isSingleton(): bool
    {
        return $this->singleton;
    }

    /**
     * Bind a parameter.
     *
     * @param string         $name
     * @param \Closure|mixed $value
     * @return self
     */
    public function bindParameter(string $name, $value): self
    {
        $this->parameters->set($name, new Parameter($name, $value));
        return $this;
    }

    /**
     * Get the instance.
     *
     * @param \Psr\Container\ContainerInterface $container
     * @return mixed
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function getInstance(ContainerInterface $container)
    {
        return ($this->closure)($container, $this->parameters);
    }
}
