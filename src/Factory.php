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
     * @var \Closure
     */
    protected $closure;

    /**
     * @var \Dionchaika\Container\ParameterCollection
     */
    protected $parameters;

    /**
     * @param \Closure $closure
     */
    public function __construct(Closure $closure)
    {
        $this->closure = $closure;
    }

    /**
     * @param string         $name
     * @param \Closure|mixed $value
     * @return self
     */
    public function bindParameter(string $name, $value): self
    {
        $this->parameters->set($name, new Parameter($value));
        return $this;
    }

    /**
     * @param string             $name
     * @param ParameterInterface $parameter
     * @return self
     */
    public function setParameter(string $name, ParameterInterface $parameter): self
    {
        $this->parameters->set($name, $parameter);
        return $this;
    }

    /**
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
