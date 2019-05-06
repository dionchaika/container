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

class ParameterCollection
{
    /**
     * @var mixed[]
     */
    protected $parameters = [];

    /**
     * @param string                                   $name
     * @param \Dionchaika\Container\ParameterInterface $parameter
     * @return void
     */
    public function set(string $name, ParameterInterface $parameter)
    {
        $this->parameters[$name] = $parameter;
    }

    /**
     * @return mixed[]
     */
    public function getAll(): array
    {
        return $this->parameters;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->parameters[$name]);
    }

    /**
     * @param string $name
     * @return \Dionchaika\Container\ParameterInterface|null
     */
    public function get(string $name): ?ParameterInterface
    {
        return $this->has($name) ? $this->parameters[$name] : null;
    }
}
