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

/**
 * <code>
 *      $container = new Container;
 *
 *      $container->bind('db', \PDO::class)
 *          ->asSingleton()
 *          ->bindParameter('passwd', $passwd)
 *          ->bindParameter('username', $username)
 *          ->bindParameter('dsn', function ($container) {
 *              return $container->getParameter('db.dsn');
 *          });
 *
 *      if ($container->has('db')) {
 *          $db = $container->get('db');
 *      }
 * </code>
 */
class Container implements ContainerInterface
{
    
}
