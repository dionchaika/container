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

use Closure;
use Psr\Container\ContainerInterface;

class Parameter implements ParameterInterface
{
    /**
     * @var \Closure|mixed
     */
    protected $value;

    /**
     * @param \Closure|mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @param \Psr\Container\ContainerInterface $container
     * @return mixed
     */
    public function getValue(ContainerInterface $container)
    {
        if (!($this->value
            instanceof Closure
        )) {
            return $this->value;
        }

        return ($this->value)($container);
    }
}
