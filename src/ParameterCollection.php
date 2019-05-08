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
use Dionchaika\Container\Interfaces\ParameterInterface;

class ParameterCollection
{
    /**
     * The array
     * of factory parameters.
     *
     * @var mixed[]
     */
    protected $parameters = [];

    /**
     * @param \Dionchaika\Container\Interfaces\ParameterInterface[] $parameters
     * @throws \InvalidArgumentException
     */
    public function __construct(array $parameters = [])
    {
        foreach ($parameters as $parameter) {
            if ($parameter instanceof ParameterInterface) {
                $this->parameters[$parameter->getName()] = $parameter;
            } else {
                throw new InvalidArgumentException(
                    'Parameter must be an instance of "\\Dionchaika\\Container\\Interfaces\\ParameterInterface"!'
                );
            }
        }
    }

    /**
     * Set a new factory
     * parameter to the collection.
     *
     * @param \Dionchaika\Container\Interfaces\ParameterInterface $parameter
     * @return \Dionchaika\Container\Interfaces\ParameterInterface
     */
    public function set(ParameterInterface $parameter): ParameterInterface
    {
        return $this->parameters[$parameter->getName()] = $parameter;
    }

    /**
     * Check is the factory
     * parameter exists in the collection.
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->parameters[$name]);
    }

    /**
     * Get the factory parameter.
     *
     * @param string $name
     * @return \Dionchaika\Container\Interfaces\ParameterInterface|null
     */
    public function get(string $name): ?ParameterInterface
    {
        return $this->has($name) ? $this->parameters[$name] : null;
    }
}
