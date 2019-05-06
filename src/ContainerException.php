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

use Exception;
use Psr\Container\ContainerExceptionInterface;

/**
 * The DI container exception class.
 *
 * @see https://www.php-fig.org/psr/psr-11/
 */
class ContainerException extends Exception implements ContainerExceptionInterface {}
