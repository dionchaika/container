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
use Dionchaika\Container\Interfaces\ParameterInterface;

class Parameter implements ParameterInterface
{
    /**
     * The parameter name.
     *
     * @var string
     */
    protected $name;

    /**
     * The parameter value.
     *
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
     * Get the parameter name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the parameter value.
     *
     * @param \Psr\Container\ContainerInterface $container
     * @return mixed
     */
    public function getValue(ContainerInterface $container)
    {
        return ($this->value instanceof Closure) ? ($this->value)($container) : $this->value;
    }
}
