<?php

namespace ForkCMS\Modules\OAuth\DependencyInjection;

use ForkCMS\Core\Domain\Router\ModuleRouteProviderInterface;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RouteCollection;

final class OAuthRouteLoader implements ModuleRouteProviderInterface
{
    public function __construct(private readonly YamlFileLoader $yamlFileLoader)
    {
    }

    public function getRouteCollection(): RouteCollection
    {
        return $this->yamlFileLoader->load(__DIR__ . '/../config/routes.yaml');
    }
}
