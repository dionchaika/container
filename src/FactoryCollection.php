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

class FactoryCollection
{
    /**
     * @var mixed[]
     */
    protected $factories = [];

    /**
     * @param string                                 $id
     * @param \Dionchaika\Container\FactoryInterface $factory
     * @return \Dionchaika\Container\FactoryInterface
     */
    public function set(string $id, FactoryInterface $factory): FactoryInterface
    {
        return $this->factories[$id] = $factory;
    }

    /**
     * @return mixed[]
     */
    public function getAll(): array
    {
        return $this->factories;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->factories[$id]);
    }

    /**
     * @param string $id
     * @return \Dionchaika\Container\FactoryInterface|null
     */
    public function get(string $id): ?FactoryInterface
    {
        return $this->has($id) ? $this->factories[$id] : null;
    }
}
