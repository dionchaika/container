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
use Dionchaika\Container\Resolvers\ConstructorResolver;

/**
 * <code>
 *      $container = new Container;
 *
 *      $container->bind('db', \PDO::class)
 *          ->asSingleton()
 *          ->bindParameter('passwd', $passwd)
 *          ->bindParameter('username', $username)
 *          ->bindParameter('dsn', function ($container) {
 *              $host = $container->getParameter('db.host');
 *              $dbname = $container->getParameter('db.name');
 *              $charset = $container->getParameter('db.charset');
 *
 *              return "mysql:host={$host};dbname={$dbname};charset={$charset}";
 *          });
 *
 *      if ($container->has('db')) {
 *          $db = $container->get('db');
 *      }
 * </code>
 */
class Container implements ContainerInterface
{
    /**
     * The container resolver.
     *
     * @var \Dionchaika\Container\ResolverInterface
     */
    protected $resolver;

    /**
     * The array
     * of container factories.
     *
     * @var \Dionchaika\Container\FactoryInterface[]
     */
    protected $factories = [];

    /**
     * @param \Dionchaika\Container\ResolverInterface|null $resolver
     */
    public function __construct(?ResolverInterface $resolver = null)
    {
        $this->resolver = $resolver ?? new ConstructorResolver;
    }

    /**
     * Bind a new type.
     *
     * @param string                     $id
     * @param \Closure|string|mixed|null $type
     * @param bool                       $singleton
     * @return \Dionchaika\Container\Factory
     */
    public function bind($id, $type = null, bool $singleton = false): Factory
    {
        
    }
}
