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

class Factory implements FactoryInterface
{
    /**
     * @var \Closure
     */
    protected $closure;

    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * @var \Dionchaika\Container\ParameterInterface[]
     */
    protected $parameters = [];

    /**
     * @param string         $name
     * @param \Closure|mixed $value
     * @return self
     */
    public function bindParameter(string $name, $value): self
    {
        $this->parameters[$name] = new Parameter($name, $value);
        return $this;
    }
}
