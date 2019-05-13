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

class FactoryCollection
{
    /**
     * The array of factories.
     *
     * @var mixed[]
     */
    protected $factories = [];

    /**
     * @param \Dionchaika\Container\FactoryInterface[] $factories
     * @throws \InvalidArgumentException
     */
    public function __construct(array $factories = [])
    {
        foreach ($factories as $factory) {
            if ($factory instanceof FactoryInterface) {
                $this->add($factory);
            } else {
                throw new InvalidArgumentException(
                    'Factory must be an instance of '
                    .'\\Dionchaika\\Container\\FactoryInterface!'
                );
            }
        }
    }

    /**
     * Add a new factory
     * to the collection.
     *
     * @param \Dionchaika\Container\FactoryInterface $factory
     * @return \Dionchaika\Container\FactoryInterface
     */
    public function add(FactoryInterface $factory): FactoryInterface
    {
        return $this->factories[$factory->getName()] = $factory;
    }

    /**
     * Get all factories
     * of the collection.
     *
     * @return mixed[]
     */
    public function all(): array
    {
        return $this->factories;
    }

    /**
     * Check is the factory
     * exists in the collection.
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->factories[$name]);
    }

    /**
     * Get factory.
     *
     * @param string $name
     * @return \Dionchaika\Container\FactoryInterface|null
     */
    public function get(string $name): ?FactoryInterface
    {
        return $this->has($name) ? $this->factories[$name] : null;
    }

    /**
     * Delete a factory
     * from the collection.
     *
     * @param string $name
     * @return self
     */
    public function delete(string $name): self
    {
        unset($this->factories[$name]);
        return $this;
    }
}
