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
     * The factory ID.
     *
     * @var string
     */
    protected $id;

    /**
     * The factory closure.
     *
     * @var \Closure
     */
    protected $closure;

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
