<?php

namespace ForkCMS\Modules\Installer\DependencyInjection;

use ForkCMS\Core\Domain\DependencyInjection\ForkModuleExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class InstallerExtension extends ForkModuleExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $this->getLoader($container)->load('services.yaml');

        if (!$container->getParameter('fork.is_installed')) {
            $this->getLoader($container)->load('services_install.yaml');
        }
    }
}
