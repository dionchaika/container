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

class Factory implements FactoryInterface
{
    /**
     * @var \Closure
     */
    protected $closure;

    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * @var \Dionchaika\Container\ParameterCollection
     */
    protected $parameters;

    /**
     * @param \Psr\Container\ContainerInterface $container
     * @return mixed
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function getInstance(ContainerInterface $container)
    {
        
    }
}
