<?php

namespace Backend\Modules\MediaLibrary\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class StorageProviderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('media_library.manager.storage')) {
            return;
        }

        $definition = $container->findDefinition('media_library.manager.storage');
        $taggedServices = $container->findTaggedServiceIds('media_library.storage_provider');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                // add the StorageProvider service to the StorageManager service
                $definition->addMethodCall('addStorageProvider', [
                    new Reference($id),
                    $attributes['storageType'],
                ]);
            }
        }
    }
}
