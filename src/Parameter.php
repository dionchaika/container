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
     * @var string
     */
    protected $name;

    /**
     * @var \Closure|mixed
     */
    protected $value;

    /**
     * @param string         $name
     * @param \Closure|mixed $value
     */
    public function __construct(string $name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param \Psr\Container\ContainerInterface $container
     * @return mixed
     */
    public function getValue(ContainerInterface $container)
    {
        if ($this->value instanceof Closure) {
            return ($this->value)($container);
        }

        return $this->value;
    }
}
