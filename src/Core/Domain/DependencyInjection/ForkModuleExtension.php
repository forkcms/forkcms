<?php

namespace ForkCMS\Core\Domain\DependencyInjection;

use ReflectionClass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Base class to load the config for your module. Prepends Fork CMS specific defaults and tagged services.
 */
abstract class ForkModuleExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
    }

    public function prepend(ContainerBuilder $container): void
    {
    }

    final protected function getLoader(ContainerBuilder $container): YamlFileLoader
    {
        $reflector = new ReflectionClass(static::class);

        return new ForkYamlFileLoader(
            $container,
            new FileLocator(((string) dirname($reflector->getFileName())) . '/../config')
        );
    }
}
