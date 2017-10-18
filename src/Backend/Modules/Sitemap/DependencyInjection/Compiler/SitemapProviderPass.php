<?php

namespace Backend\Modules\Sitemap\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class SitemapProviderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('sitemap.manager')) {
            return;
        }

        $definition = $container->findDefinition('sitemap.manager');
        $taggedServices = $container->findTaggedServiceIds('sitemap.provider');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                // add the SitemapProvider service to the SitemapManager service
                $definition->addMethodCall('addSitemapProvider', [
                    new Reference($id),
                ]);
            }
        }
    }
}
