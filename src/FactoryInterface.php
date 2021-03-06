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

use Psr\Container\ContainerInterface;

interface FactoryInterface
{
    /**
     * Get the factory name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Check is the resolving
     * instance should be managed as a singleton.
     *
     * @return bool
     */
    public function isSingleton(): bool;

    /**
     * Get instance.
     *
     * @param \Psr\Container\ContainerInterface $container
     * @return mixed
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function getInstance(ContainerInterface $container);
}
