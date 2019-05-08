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

interface ParameterInterface
{
    /**
     * Get the parameter name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the parameter value.
     *
     * @param \Psr\Container\ContainerInterface $container
     * @return mixed
     */
    public function getValue(ContainerInterface $container);
}
