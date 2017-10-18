<?php

namespace ForkCMS\App;

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

    public static function get(string $serviceId)
    {
        return self::$container->get($serviceId);
    }

    public static function getContainer(): ContainerInterface
    {
        return self::$container;
    }

    public static function has(string $serviceId): bool
    {
        return self::$container->has($serviceId);
    }

    public static function setContainer(ContainerInterface $container = null): void
    {
        self::$container = $container;
    }
}
