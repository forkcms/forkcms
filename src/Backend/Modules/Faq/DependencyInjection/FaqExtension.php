<?php

namespace ForkCMS\Backend\Modules\Faq\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class FaqExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->getLoader($container)->load('services.yml');
    }

    public function prepend(ContainerBuilder $container)
    {
        $this->getLoader($container)->load('doctrine.yml');
    }

    public function getLoader(ContainerBuilder $container): Loader\YamlFileLoader
    {
        return new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
    }
}
