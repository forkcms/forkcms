<?php

namespace ForkCMS\Modules\Frontend\DependencyInjection;

use ForkCMS\Core\Domain\Router\ModuleRouteProviderInterface;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RouteCollection;

final class FrontendRouteLoader implements ModuleRouteProviderInterface
{
    public function __construct(private YamlFileLoader $yamlFileLoader)
    {
    }

    public function getRouteCollection(): RouteCollection
    {
        return $this->yamlFileLoader->load(__DIR__ . '/../config/routes.yaml');
    }
}
