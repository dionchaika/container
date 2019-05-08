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
use Dionchaika\Container\Interfaces\FactoryInterface;

class FactoryCollection
{
    /**
     * The array
     * of instance factories.
     *
     * @var mixed[]
     */
    protected $factories = [];

    /**
     * @param \Dionchaika\Container\Interfaces\FactoryInterface[] $factories
     * @throws \InvalidArgumentException
     */
    public function __construct(array $factories = [])
    {
        foreach ($factories as $factory) {
            if ($factory instanceof FactoryInterface) {
                $this->factories[$factory->getName()] = $factory;
            } else {
                throw new InvalidArgumentException(
                    'Factory must be an instance of "\\Dionchaika\\Container\\Interfaces\\FactoryInterface"!'
                );
            }
        }
    }

    /**
     * Set a new instance
     * factory to the collection.
     *
     * @param \Dionchaika\Container\Interfaces\FactoryInterface $factory
     * @return \Dionchaika\Container\Interfaces\FactoryInterface
     */
    public function set(FactoryInterface $factory): FactoryInterface
    {
        return $this->factories[$factory->getName()] = $factory;
    }

    /**
     * Check is the instance
     * factory exists in the collection.
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->factories[$name]);
    }

    /**
     * Get the instance factory.
     *
     * @param string $name
     * @return \Dionchaika\Container\Interfaces\FactoryInterface|null
     */
    public function get(string $name): ?FactoryInterface
    {
        return $this->has($name) ? $this->factories[$name] : null;
    }
}
