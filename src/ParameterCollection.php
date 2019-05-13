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

use InvalidArgumentException;

class ParameterCollection
{
    /**
     * The array of parameters.
     *
     * @var mixed[]
     */
    protected $parameters = [];

    /**
     * @param \Dionchaika\Container\ParameterInterface[] $parameters
     * @throws \InvalidArgumentException
     */
    public function __construct(array $parameters = [])
    {
        foreach ($parameters as $parameter) {
            if ($parameter instanceof ParameterInterface) {
                $this->add($parameter);
            } else {
                throw new InvalidArgumentException(
                    'Parameter must be an instance of '
                    .'\\Dionchaika\\Container\\ParameterInterface!'
                );
            }
        }
    }

    /**
     * Add a new parameter to the collection.
     *
     * @param \Dionchaika\Container\ParameterInterface $parameter
     * @return \Dionchaika\Container\ParameterInterface
     */
    public function add(ParameterInterface $parameter): ParameterInterface
    {
        return $this->parameters[$parameter->getName()] = $parameter;
    }

    /**
     * Get all parameters of the collection.
     *
     * @return mixed[]
     */
    public function all(): array
    {
        return $this->parameters;
    }

    /**
     * Check is the parameter
     * exists in the collection.
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->parameters[$name]);
    }

    /**
     * Get parameter.
     *
     * @param string $name
     * @return \Dionchaika\Container\ParameterInterface|null
     */
    public function get(string $name): ?ParameterInterface
    {
        return $this->has($name) ? $this->parameters[$name] : null;
    }

    /**
     * Delete a parameter
     * from the collection.
     *
     * @param string $name
     * @return self
     */
    public function delete(string $name): self
    {
        unset($this->parameters[$name]);
        return $this;
    }
}
