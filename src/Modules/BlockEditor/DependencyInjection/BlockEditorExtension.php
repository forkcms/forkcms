<?php

namespace ForkCMS\Modules\BlockEditor\DependencyInjection;

use ForkCMS\Core\Domain\DependencyInjection\ForkModuleExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class BlockEditorExtension extends ForkModuleExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $this->getLoader($container)->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->getLoader($container)->load('cache.yaml');
    }

//    public function process(ContainerBuilder $container): void
//    {
//        $container->prependExtensionConfig(
//            'framework',
//            [
//                'cache' => [
//                    'pools' => [
//                        'cache.blockEditor' => [
//                            'adapter' => $container->getParameter('kernel.environment') === 'dev' ?
//                                'cache.adapter.array' : 'cache.app',
//                            'public' => true,
//                            'default_lifetime' => 31522400, // one year
//                        ],
//                    ],
//                ],
//            ]
//        );
//    }
}
