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
     * @param \Dionchaika\Container\ParameterInterface|string $name
     * @param \Closure|mixed                                  $value
     * @return self
     * @throws \InvalidArgumentException
     */
    public function bindParameter($name, $value): self
    {
        if ($name instanceof ParameterInterface) {
            $this->parameters[$name->getName()] = $name;
        } else if (is_string($name)) {
            $this->parameters[$name] = new Parameter($name, $value);
        } else {
            throw new InvalidArgumentException(
                'Invalid parameter name! Parameter name must be a string.'
            );
        }

        return $this;
    }
}
