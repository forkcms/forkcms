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
        foreach ($container->getParameter('installed_modules') as $module) {
            $dir = $container->getParameter('kernel.root_dir') . '/../src/Backend/Modules/' . $module . '/Entity';

            if (!$filesystem->exists($dir)) {
                continue;
            }

            // @TODO Check for YAML/XML files and set the type accordingly
            $container->prependExtensionConfig(
                'doctrine',
                array(
                    'orm' => array (
                        'mappings' => array (
                            $module => array (
                                'type' => 'annotation',
                                'is_bundle' => false,
                                'dir' => $dir,
                                'prefix' => 'Backend\\Modules\\' . $module . '\\Entity',
                                'alias' => $module,
                            ),
                        ),
                    ),
                )
            );
        }
    }
}
