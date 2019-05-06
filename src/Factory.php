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

/**
 * The factory class
 * for the DI container.
 */
class Factory implements FactoryInterface
{
    /**
     * The factory identifier.
     *
     * @var string
     */
    protected $id;

    /**
     * The factory alias name.
     *
     * @var string
     */
    protected $alias;

    /**
     * The factory closure instance.
     *
     * @var \Closure
     */
    protected $closure;

    /**
     * The factory container instance.
     *
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * The array of
     * factory parameters.
     *
     * @var \Parameter[]
     */
    protected $parameters = [];

    /**
     * Is the resolved instance
     * should be managed as a singleton.
     *
     * @var bool
     */
    protected $singleton = false;
}
