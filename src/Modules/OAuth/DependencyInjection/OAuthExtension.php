<?php

namespace ForkCMS\Modules\OAuth\DependencyInjection;

use ForkCMS\Core\Domain\DependencyInjection\ForkModuleExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class OAuthExtension extends ForkModuleExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $this->getLoader($container)->load('services.yaml');
        if ($container->getParameter('fork.is_installed')) {
            $this->getLoader($container)->load('controllers.yaml');
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->getLoader($container)->load('doctrine.yaml');
    }
}
