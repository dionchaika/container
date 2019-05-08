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

interface ResolverInterface
{
    /**
     * Resolve an instance of the type.
     *
     * @param \Psr\Container\ContainerInterface $container
     * @param string                            $type
     * @return mixed
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function resolve(ContainerInterface $container, string $type);
}
