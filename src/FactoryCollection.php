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
     * The array of all of the factories.
     *
     * @var \Dionchaika\Container\FactoryInterface[]
     */
    protected $allFactories = [];

    /**
     * @param \Dionchaika\Container\FactoryInterface[] $factories
     * @throws \InvalidArgumentException
     */
    public function __construct(array $factories = [])
    {
        foreach ($factories as $factory) {
            if (!($factory instanceof FactoryInterface)) {
                throw new InvalidArgumentException(
                    'Factory must be an instance of '
                    .'\\Dionchaika\\Container\\FactoryInterface!'
                );
            }

            $this->add($factory);
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
        $this->allFactories[] = $factory;
        return $this->factories[$factory->getName()] = $factory;
    }

    /**
     * Get all of the factories in the collection.
     *
     * @return \Dionchaika\Container\FactoryInterface[]
     */
    public function all(): array
    {
        return $this->allFactories;
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
