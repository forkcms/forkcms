<?php

namespace Backend\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class BackendExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // Nothing needs to be loaded here
    }

    /**
     * @inheritDoc
     */
    public function prepend(ContainerBuilder $container)
    {
        $filesystem = new Filesystem();
        foreach ((array) $container->getParameter('installed_modules') as $module) {
            $dir = $container->getParameter('kernel.root_dir') . '/../src/Backend/Modules/' . $module . '/Entity';

            if (!$filesystem->exists($dir)) {
                continue;
            }

            /*
             * Find and load entities in the backend folder.
             * We do this by looping all installed modules and looking for an Entity directory.
             * If the Entity map is found, a configuration will be prepended to the configuration.
             * So it's basically like if you would add every single module by hand, but automagically.
             *
             * @TODO Check for YAML/XML files and set the type accordingly
             */
            $container->prependExtensionConfig(
                'doctrine',
                array(
                    'orm' => array(
                        'mappings' => array(
                            $module => array(
                                'type' => 'annotation',
                                'is_bundle' => false,
                                'dir' => $dir,
                                'prefix' => 'Backend\\Modules\\' . $module . '\\Entity',
                            ),
                        ),
                    ),
                )
            );
        }
    }
}
