<?php

namespace ForkCMS\App;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The base model's responsibility is to provide the service container to
 * both FrontendModel and BackendModel.
 *
 * In the long run models should not be a collection of static methods, and this will disappear.
 */
class BaseModel
{
    /**
     * @var ContainerInterface
     */
    private static $container;

    /**
     * Gets a service by id.
     *
     * @param string $reference The service id
     *
     * @return object The service
     */
    public static function get($reference)
    {
        return self::$container->get($reference);
    }

    /**
     * @return ContainerInterface
     */
    public static function getContainer()
    {
        return self::$container;
    }

    /**
     * Returns true if the service id is defined.
     *
     * @param string $reference The service id
     *
     * @return Boolean true if the service id is defined, false otherwise
     */
    public static function has($reference)
    {
        return self::$container->has($reference);
    }

    /**
     * @param ContainerInterface $container
     */
    public static function setContainer(ContainerInterface $container = null)
    {
        self::$container = $container;
    }
}
