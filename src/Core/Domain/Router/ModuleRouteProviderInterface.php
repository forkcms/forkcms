<?php

namespace ForkCMS\Core\Domain\Router;

use Symfony\Component\Routing\RouteCollection;

interface ModuleRouteProviderInterface
{
    public function getRouteCollection(): RouteCollection;
}
